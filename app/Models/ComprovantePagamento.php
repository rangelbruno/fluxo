<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComprovantePagamento extends Model
{
    use SoftDeletes;

    protected $table = 'comprovantes_pagamento';

    protected $fillable = [
        'data_pagamento',
        'valor',
        'descricao',
        'referencia',
        'pagador',
        'documento_pagador',
        'favorecido',
        'documento_favorecido',
        'forma_pagamento',
        'identificador_transacao',
        'codigo_autenticacao',
        'banco_origem',
        'agencia_origem',
        'conta_origem',
        'banco_destino',
        'agencia_destino',
        'conta_destino',
        'chave_pix',
        'tipo_chave_pix',
        'arquivo_original_url',
        'arquivo_original_nome',
        'arquivo_tipo',
        'arquivo_tamanho',
        'confianca_extracao',
        'texto_ocr',
        'dados_brutos_ia',
        'observacoes_ia',
        'status',
        'user_id',
        'origem',
        'workflow_execution_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_pagamento' => 'date',
            'valor' => 'decimal:2',
            'dados_brutos_ia' => 'array',
            'confianca_extracao' => 'decimal:2',
            'arquivo_tamanho' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar por forma de pagamento
     */
    public function scopeFormaPagamento($query, string $forma)
    {
        return $query->where('forma_pagamento', $forma);
    }

    /**
     * Scope para filtrar por período
     */
    public function scopePeriodo($query, string $inicio, string $fim)
    {
        return $query->whereBetween('data_pagamento', [$inicio, $fim]);
    }
}
