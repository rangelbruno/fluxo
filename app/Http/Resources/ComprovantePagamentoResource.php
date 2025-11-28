<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComprovantePagamentoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // Dados principais
            'data_pagamento' => $this->data_pagamento?->format('Y-m-d'),
            'valor' => (float) $this->valor,
            'valor_formatado' => 'R$ ' . number_format((float) $this->valor, 2, ',', '.'),
            'descricao' => $this->descricao,
            'referencia' => $this->referencia,

            // Partes envolvidas
            'pagador' => $this->pagador,
            'documento_pagador' => $this->documento_pagador,
            'favorecido' => $this->favorecido,
            'documento_favorecido' => $this->documento_favorecido,

            // Transação
            'forma_pagamento' => $this->forma_pagamento,
            'identificador_transacao' => $this->identificador_transacao,
            'codigo_autenticacao' => $this->codigo_autenticacao,

            // Dados bancários origem
            'banco_origem' => $this->banco_origem,
            'agencia_origem' => $this->agencia_origem,
            'conta_origem' => $this->conta_origem,

            // Dados bancários destino
            'banco_destino' => $this->banco_destino,
            'agencia_destino' => $this->agencia_destino,
            'conta_destino' => $this->conta_destino,

            // PIX
            'chave_pix' => $this->chave_pix,
            'tipo_chave_pix' => $this->tipo_chave_pix,

            // Arquivo
            'arquivo' => [
                'url' => $this->arquivo_original_url,
                'nome' => $this->arquivo_original_nome,
                'tipo' => $this->arquivo_tipo,
                'tamanho' => $this->arquivo_tamanho,
            ],

            // Metadados IA
            'ia' => [
                'confianca' => (float) $this->confianca_extracao,
                'observacoes' => $this->observacoes_ia,
            ],

            // Rastreabilidade
            'status' => $this->status,
            'origem' => $this->origem,
            'workflow_execution_id' => $this->workflow_execution_id,
            'user_id' => $this->user_id,

            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
