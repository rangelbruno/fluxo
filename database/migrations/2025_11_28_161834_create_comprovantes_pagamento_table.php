<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comprovantes_pagamento', function (Blueprint $table) {
            $table->id();

            // Dados principais do pagamento
            $table->date('data_pagamento')->nullable();
            $table->decimal('valor', 15, 2)->nullable();
            $table->string('descricao', 500)->nullable();
            $table->string('referencia', 255)->nullable();

            // Partes envolvidas
            $table->string('pagador', 255)->nullable();
            $table->string('documento_pagador', 20)->nullable(); // CPF/CNPJ
            $table->string('favorecido', 255)->nullable();
            $table->string('documento_favorecido', 20)->nullable(); // CPF/CNPJ

            // Informações da transação
            $table->string('forma_pagamento', 50)->nullable(); // PIX, TED, DOC, Boleto, Cartão
            $table->string('identificador_transacao', 255)->nullable();
            $table->string('codigo_autenticacao', 255)->nullable();

            // Informações bancárias
            $table->string('banco_origem', 100)->nullable();
            $table->string('agencia_origem', 20)->nullable();
            $table->string('conta_origem', 30)->nullable();
            $table->string('banco_destino', 100)->nullable();
            $table->string('agencia_destino', 20)->nullable();
            $table->string('conta_destino', 30)->nullable();

            // Chave PIX (quando aplicável)
            $table->string('chave_pix', 255)->nullable();
            $table->string('tipo_chave_pix', 20)->nullable(); // CPF, CNPJ, Email, Telefone, Aleatoria

            // Arquivo original
            $table->string('arquivo_original_url', 500)->nullable();
            $table->string('arquivo_original_nome', 255)->nullable();
            $table->string('arquivo_tipo', 20)->nullable(); // pdf, jpg, png
            $table->unsignedInteger('arquivo_tamanho')->nullable(); // bytes

            // Metadados da extração por IA
            $table->decimal('confianca_extracao', 5, 2)->nullable(); // 0-100%
            $table->text('texto_ocr')->nullable(); // Texto extraído do OCR
            $table->json('dados_brutos_ia')->nullable(); // JSON completo retornado pela IA
            $table->text('observacoes_ia')->nullable(); // Campos ambíguos ou alertas

            // Rastreabilidade
            $table->string('status', 30)->default('processado'); // pendente, processado, erro, revisao_manual
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('origem', 50)->default('n8n'); // n8n, api, manual
            $table->string('workflow_execution_id', 100)->nullable(); // ID da execução no n8n

            $table->timestamps();
            $table->softDeletes();

            // Índices para buscas comuns
            $table->index('data_pagamento');
            $table->index('forma_pagamento');
            $table->index('status');
            $table->index('identificador_transacao');
            $table->index('documento_pagador');
            $table->index('documento_favorecido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprovantes_pagamento');
    }
};
