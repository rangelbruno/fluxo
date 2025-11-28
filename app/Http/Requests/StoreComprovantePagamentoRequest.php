<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreComprovantePagamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Para integração com n8n, usamos token de API ou IP whitelist
     */
    public function authorize(): bool
    {
        // Verificar token de API no header
        $apiToken = $this->header('X-API-Token');
        $expectedToken = config('services.n8n.api_token');

        if ($expectedToken && $apiToken === $expectedToken) {
            return true;
        }

        // Ou verificar se é uma requisição interna (mesmo servidor)
        $allowedIps = config('services.n8n.allowed_ips', ['127.0.0.1', '::1']);
        if (in_array($this->ip(), $allowedIps)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data_pagamento' => ['nullable', 'date', 'before_or_equal:today'],
            'valor' => ['nullable', 'numeric', 'min:0', 'max:99999999999.99'],
            'descricao' => ['nullable', 'string', 'max:500'],
            'referencia' => ['nullable', 'string', 'max:255'],

            'pagador' => ['nullable', 'string', 'max:255'],
            'documento_pagador' => ['nullable', 'string', 'max:20'],
            'favorecido' => ['nullable', 'string', 'max:255'],
            'documento_favorecido' => ['nullable', 'string', 'max:20'],

            'forma_pagamento' => ['nullable', 'string', 'max:50'],
            'identificador_transacao' => ['nullable', 'string', 'max:255'],
            'codigo_autenticacao' => ['nullable', 'string', 'max:255'],

            'banco_origem' => ['nullable', 'string', 'max:100'],
            'agencia_origem' => ['nullable', 'string', 'max:20'],
            'conta_origem' => ['nullable', 'string', 'max:30'],
            'banco_destino' => ['nullable', 'string', 'max:100'],
            'agencia_destino' => ['nullable', 'string', 'max:20'],
            'conta_destino' => ['nullable', 'string', 'max:30'],

            'chave_pix' => ['nullable', 'string', 'max:255'],
            'tipo_chave_pix' => ['nullable', 'string', 'in:CPF,CNPJ,Email,Telefone,Aleatoria'],

            'arquivo_original_url' => ['nullable', 'url', 'max:500'],
            'arquivo_original_nome' => ['nullable', 'string', 'max:255'],
            'arquivo_tipo' => ['nullable', 'string', 'in:pdf,jpg,jpeg,png'],
            'arquivo_tamanho' => ['nullable', 'integer', 'min:0'],

            'confianca_extracao' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'texto_ocr' => ['nullable', 'string'],
            'dados_brutos_ia' => ['nullable', 'array'],
            'observacoes_ia' => ['nullable', 'string'],

            'status' => ['nullable', 'string', 'in:pendente,processado,erro,revisao_manual'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'origem' => ['nullable', 'string', 'max:50'],
            'workflow_execution_id' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'data_pagamento.before_or_equal' => 'A data de pagamento não pode ser futura.',
            'valor.max' => 'O valor excede o limite máximo permitido.',
            'tipo_chave_pix.in' => 'Tipo de chave PIX inválido. Use: CPF, CNPJ, Email, Telefone ou Aleatoria.',
            'arquivo_tipo.in' => 'Tipo de arquivo inválido. Use: pdf, jpg, jpeg ou png.',
            'status.in' => 'Status inválido. Use: pendente, processado, erro ou revisao_manual.',
        ];
    }

    /**
     * Handle a failed validation attempt for API responses.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Handle a failed authorization attempt for API responses.
     */
    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Não autorizado. Verifique o token de API.',
            ], 403)
        );
    }
}
