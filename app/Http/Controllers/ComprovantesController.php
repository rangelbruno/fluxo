<?php

namespace App\Http\Controllers;

use App\Models\ComprovantePagamento;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ComprovantesController extends Controller
{
    /**
     * Exibe a listagem de comprovantes.
     */
    public function index(Request $request): View
    {
        $query = ComprovantePagamento::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('forma_pagamento')) {
            $query->where('forma_pagamento', $request->forma_pagamento);
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_pagamento', [$request->data_inicio, $request->data_fim]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pagador', 'ilike', "%{$search}%")
                    ->orWhere('favorecido', 'ilike', "%{$search}%")
                    ->orWhere('descricao', 'ilike', "%{$search}%");
            });
        }

        $comprovantes = $query->orderByDesc('created_at')->paginate(15);

        $stats = [
            'total' => ComprovantePagamento::count(),
            'processados' => ComprovantePagamento::where('status', 'processado')->count(),
            'pendentes' => ComprovantePagamento::where('status', 'pendente')->count(),
            'erros' => ComprovantePagamento::where('status', 'erro')->count(),
        ];

        return view('comprovantes.index', compact('comprovantes', 'stats'));
    }

    /**
     * Processa o upload com IA (OpenAI ou Groq) e salva no banco.
     */
    public function processar(Request $request): JsonResponse
    {
        $request->validate([
            'arquivo' => 'required_without:texto|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'texto' => 'required_without:arquivo|string|nullable',
        ]);

        try {
            $textoComprovante = $request->input('texto', '');
            $fileData = [];
            $imageBase64 = null;

            // Se enviou arquivo, processar
            if ($request->hasFile('arquivo')) {
                $file = $request->file('arquivo');
                $fileData = [
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                ];

                $extension = strtolower($file->getClientOriginalExtension());

                // Se for imagem, converter para base64 para visao
                if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    $imageBase64 = base64_encode(file_get_contents($file->getRealPath()));
                    $mimeType = $file->getMimeType();
                }
            }

            // Chamar IA apropriada
            if ($imageBase64) {
                // Usar modelo de visao para imagens
                $iaResult = $this->chamarIAVisao($imageBase64, $mimeType);
            } else {
                // Usar modelo de texto
                $iaResult = $this->chamarIA($textoComprovante);
            }

            // Salvar no banco
            $comprovante = ComprovantePagamento::create([
                'data_pagamento' => $this->normalizeDate($iaResult['data']['data_pagamento'] ?? null),
                'valor' => $this->normalizeValue($iaResult['data']['valor'] ?? null),
                'pagador' => $iaResult['data']['pagador'] ?? null,
                'favorecido' => $iaResult['data']['favorecido'] ?? null,
                'forma_pagamento' => $iaResult['data']['forma_pagamento'] ?? null,
                'identificador_transacao' => $iaResult['data']['identificador_transacao'] ?? null,
                'banco_origem' => $iaResult['data']['banco_origem'] ?? null,
                'banco_destino' => $iaResult['data']['banco_destino'] ?? null,
                'arquivo_original_nome' => $fileData['file_name'] ?? null,
                'arquivo_tipo' => $fileData['file_type'] ?? null,
                'arquivo_tamanho' => $fileData['file_size'] ?? null,
                'dados_brutos_ia' => $iaResult['data'],
                'confianca_extracao' => 85,
                'status' => 'processado',
                'user_id' => auth()->id(),
                'origem' => 'web',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comprovante processado com ' . $iaResult['provider'] . '!',
                'data' => $comprovante,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao processar comprovante', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar comprovante: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Chama a IA com modelo de visao para analisar imagem.
     */
    private function chamarIAVisao(string $imageBase64, string $mimeType): array
    {
        $systemPrompt = 'Voce e um especialista em analise de comprovantes de pagamento brasileiros. Analise a imagem do comprovante e extraia os dados. Retorne APENAS um JSON valido com: data_pagamento (YYYY-MM-DD), valor (numero), pagador, favorecido, forma_pagamento (PIX/TED/DOC/Boleto), identificador_transacao, banco_origem, banco_destino. Use null se nao encontrar.';

        // Tentar OpenAI GPT-4o-mini com visao primeiro
        if (config('services.openai.api_key')) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Analise este comprovante de pagamento e extraia os dados:',
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$imageBase64}",
                                ],
                            ],
                        ],
                    ],
                ],
                'temperature' => 0.1,
                'max_tokens' => 1000,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '{}');
                $jsonData = $this->extrairJSON($content);

                return [
                    'provider' => 'OpenAI Vision',
                    'data' => $jsonData,
                ];
            }

            Log::warning('OpenAI Vision falhou, tentando OCR + Groq', ['status' => $response->status()]);
        }

        // Fallback: Usar OCR gratuito para extrair texto, depois Groq para analisar
        $textoExtraido = $this->extrairTextoOCR($imageBase64);

        if (empty($textoExtraido)) {
            throw new \Exception('Nao foi possivel extrair texto da imagem. Tente usar a aba "Colar Texto".');
        }

        Log::info('Texto extraido via OCR', ['texto' => substr($textoExtraido, 0, 200)]);

        // Usar Groq para analisar o texto extraido
        $result = $this->chamarIA($textoExtraido);
        $result['provider'] = 'OCR + ' . $result['provider'];

        return $result;
    }

    /**
     * Extrai texto de imagem usando OCR.space API (gratuito).
     */
    private function extrairTextoOCR(string $imageBase64): string
    {
        $apiKey = config('services.ocr_space.api_key', 'helloworld');

        // OCR.space API - gratuito ate 25.000 requisicoes/mes
        $response = Http::asForm()
            ->timeout(60)
            ->post('https://api.ocr.space/parse/image', [
                'apikey' => $apiKey,
                'base64Image' => 'data:image/png;base64,' . $imageBase64,
                'language' => 'por',
                'isOverlayRequired' => 'false',
                'OCREngine' => '2',
            ]);

        if ($response->successful()) {
            $result = $response->json();

            if (isset($result['ParsedResults'][0]['ParsedText'])) {
                return $result['ParsedResults'][0]['ParsedText'];
            }

            if (isset($result['ErrorMessage'])) {
                Log::warning('OCR.space erro', ['error' => $result['ErrorMessage']]);
            }

            if (isset($result['IsErroredOnProcessing']) && $result['IsErroredOnProcessing']) {
                Log::warning('OCR.space processing error', ['result' => $result]);
            }
        } else {
            Log::warning('OCR.space request failed', ['status' => $response->status()]);
        }

        return '';
    }

    /**
     * Extrai JSON de uma resposta que pode conter texto adicional.
     */
    private function extrairJSON(string $content): array
    {
        // Tenta decodificar diretamente
        $data = json_decode($content, true);
        if ($data !== null) {
            return $data;
        }

        // Procura por JSON dentro do texto
        if (preg_match('/\{[^{}]*\}/', $content, $matches)) {
            $data = json_decode($matches[0], true);
            if ($data !== null) {
                return $data;
            }
        }

        // Procura por JSON com quebras de linha
        if (preg_match('/```json\s*([\s\S]*?)\s*```/', $content, $matches)) {
            $data = json_decode($matches[1], true);
            if ($data !== null) {
                return $data;
            }
        }

        return [];
    }

    /**
     * Chama a IA para extrair dados de texto (OpenAI com fallback para Groq).
     */
    private function chamarIA(string $texto): array
    {
        $systemPrompt = 'Voce e um especialista em analise de comprovantes de pagamento brasileiros. Extraia os dados e retorne APENAS um JSON valido com: data_pagamento (YYYY-MM-DD), valor (numero), pagador, favorecido, forma_pagamento (PIX/TED/DOC/Boleto), identificador_transacao, banco_origem, banco_destino. Use null se nao encontrar.';
        $userPrompt = "Extraia os dados:\n\n{$texto}";

        // Tentar OpenAI primeiro
        if (config('services.openai.api_key')) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.1,
                'response_format' => ['type' => 'json_object'],
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '{}');

                return [
                    'provider' => 'OpenAI',
                    'data' => json_decode($content, true) ?? [],
                ];
            }

            Log::warning('OpenAI falhou, tentando Groq', ['status' => $response->status()]);
        }

        // Fallback para Groq
        if (config('services.groq.api_key')) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.groq.api_key'),
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.1-8b-instant',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.1,
                'response_format' => ['type' => 'json_object'],
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '{}');

                return [
                    'provider' => 'Groq',
                    'data' => json_decode($content, true) ?? [],
                ];
            }

            throw new \Exception('Groq falhou: ' . $response->status());
        }

        throw new \Exception('Nenhuma API de IA configurada');
    }

    /**
     * Normaliza data para YYYY-MM-DD.
     */
    private function normalizeDate(?string $date): ?string
    {
        if (! $date) {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        if (preg_match('/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})/', $date, $m)) {
            $year = strlen($m[3]) === 2 ? '20' . $m[3] : $m[3];

            return $year . '-' . str_pad($m[2], 2, '0', STR_PAD_LEFT) . '-' . str_pad($m[1], 2, '0', STR_PAD_LEFT);
        }

        return null;
    }

    /**
     * Normaliza valor para decimal.
     */
    private function normalizeValue(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $cleaned = preg_replace('/[R$\s.]/', '', (string) $value);
        $cleaned = str_replace(',', '.', $cleaned);

        return is_numeric($cleaned) ? (float) $cleaned : null;
    }

    /**
     * Exibe detalhes de um comprovante.
     */
    public function show(ComprovantePagamento $comprovante): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $comprovante,
        ]);
    }

    /**
     * Remove um comprovante.
     */
    public function destroy(ComprovantePagamento $comprovante): JsonResponse
    {
        $comprovante->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comprovante removido com sucesso!',
        ]);
    }
}
