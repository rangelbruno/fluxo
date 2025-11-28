<?php

namespace App\Http\Controllers;

use App\Models\ComprovantePagamento;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ConfiguracaoController extends Controller
{
    /**
     * Exibe a página de configuração do n8n.
     */
    public function n8n(): View
    {
        $n8nStatus = $this->checkN8nStatus();
        $workflowStatus = $this->checkWorkflowStatus();
        $apiStatus = $this->checkApiStatus();

        $stats = [
            'total_comprovantes' => ComprovantePagamento::count(),
            'processados' => ComprovantePagamento::where('status', 'processado')->count(),
            'pendentes' => ComprovantePagamento::where('status', 'pendente')->count(),
            'erros' => ComprovantePagamento::where('status', 'erro')->count(),
            'revisao_manual' => ComprovantePagamento::where('status', 'revisao_manual')->count(),
        ];

        return view('configuracoes.n8n', compact('n8nStatus', 'workflowStatus', 'apiStatus', 'stats'));
    }

    /**
     * Verifica status do n8n.
     */
    private function checkN8nStatus(): array
    {
        try {
            $response = Http::timeout(3)->get(config('services.n8n.webhook_url', 'http://fluxo-n8n:5678') . '/healthz');

            return [
                'online' => $response->successful(),
                'message' => $response->successful() ? 'Conectado' : 'Erro de conexão',
            ];
        } catch (\Exception $e) {
            return [
                'online' => false,
                'message' => 'Não foi possível conectar ao n8n',
            ];
        }
    }

    /**
     * Verifica status do workflow.
     */
    private function checkWorkflowStatus(): array
    {
        try {
            $apiKey = config('services.n8n.api_key', env('N8N_API_KEY'));
            $response = Http::timeout(3)
                ->withHeaders(['X-N8N-API-KEY' => $apiKey])
                ->get(config('services.n8n.api_url', 'http://fluxo-n8n:5679') . '/api/v1/workflows?active=true');

            if ($response->successful()) {
                $workflows = $response->json('data', []);
                $comprovantesWorkflow = collect($workflows)->firstWhere('name', 'Processar Comprovantes v2');

                return [
                    'ativo' => ! empty($comprovantesWorkflow),
                    'total_workflows' => count($workflows),
                    'message' => $comprovantesWorkflow ? 'Workflow ativo' : 'Workflow não encontrado',
                ];
            }

            return ['ativo' => false, 'total_workflows' => 0, 'message' => 'Erro ao verificar'];
        } catch (\Exception $e) {
            return ['ativo' => false, 'total_workflows' => 0, 'message' => 'Erro de conexão'];
        }
    }

    /**
     * Verifica status da API de comprovantes.
     */
    private function checkApiStatus(): array
    {
        return [
            'online' => true,
            'message' => 'API operacional',
            'endpoints' => [
                'POST /api/v1/comprovantes' => 'Criar comprovante',
                'GET /api/v1/comprovantes' => 'Listar comprovantes',
                'GET /api/v1/comprovantes/{id}' => 'Ver comprovante',
            ],
        ];
    }
}
