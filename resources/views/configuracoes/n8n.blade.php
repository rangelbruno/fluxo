<x-layouts.app>
    {{-- Toolbar --}}
    <div class="d-flex flex-wrap flex-stack my-5">
        <h2 class="fs-2 fw-semibold my-2">
            Integração n8n
            <span class="fs-6 text-gray-500 ms-1">Processamento de Comprovantes</span>
        </h2>
        <div class="d-flex align-items-center gap-2">
            <a href="http://localhost:5679" target="_blank" class="btn btn-sm btn-light-primary">
                <i class="ki-duotone ki-exit-right-corner fs-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Abrir n8n
            </a>
        </div>
    </div>

    {{-- Status Cards --}}
    <div class="row g-5 mb-5">
        {{-- n8n Status --}}
        <div class="col-md-4">
            <div class="card card-flush h-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">n8n Server</span>
                    </h3>
                    <div class="card-toolbar">
                        @if($n8nStatus['online'])
                            <span class="badge badge-light-success">Online</span>
                        @else
                            <span class="badge badge-light-danger">Offline</span>
                        @endif
                    </div>
                </div>
                <div class="card-body pt-5">
                    <div class="d-flex align-items-center mb-3">
                        <div class="symbol symbol-50px me-3">
                            <span class="symbol-label bg-light-{{ $n8nStatus['online'] ? 'success' : 'danger' }}">
                                <i class="ki-duotone ki-abstract-26 fs-2x text-{{ $n8nStatus['online'] ? 'success' : 'danger' }}">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-800 fw-bold fs-6">{{ $n8nStatus['message'] }}</span>
                            <span class="text-gray-500 fw-semibold d-block fs-7">http://localhost:5679</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Workflow Status --}}
        <div class="col-md-4">
            <div class="card card-flush h-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">Workflow</span>
                    </h3>
                    <div class="card-toolbar">
                        @if($workflowStatus['ativo'])
                            <span class="badge badge-light-success">Ativo</span>
                        @else
                            <span class="badge badge-light-warning">Inativo</span>
                        @endif
                    </div>
                </div>
                <div class="card-body pt-5">
                    <div class="d-flex align-items-center mb-3">
                        <div class="symbol symbol-50px me-3">
                            <span class="symbol-label bg-light-{{ $workflowStatus['ativo'] ? 'success' : 'warning' }}">
                                <i class="ki-duotone ki-arrows-circle fs-2x text-{{ $workflowStatus['ativo'] ? 'success' : 'warning' }}">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-800 fw-bold fs-6">{{ $workflowStatus['message'] }}</span>
                            <span class="text-gray-500 fw-semibold d-block fs-7">Processar Comprovantes v2</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- API Status --}}
        <div class="col-md-4">
            <div class="card card-flush h-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">API Laravel</span>
                    </h3>
                    <div class="card-toolbar">
                        <span class="badge badge-light-success">Operacional</span>
                    </div>
                </div>
                <div class="card-body pt-5">
                    <div class="d-flex align-items-center mb-3">
                        <div class="symbol symbol-50px me-3">
                            <span class="symbol-label bg-light-success">
                                <i class="ki-duotone ki-code fs-2x text-success">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-800 fw-bold fs-6">{{ $apiStatus['message'] }}</span>
                            <span class="text-gray-500 fw-semibold d-block fs-7">/api/v1/comprovantes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-5 mb-5">
        <div class="col-md-3">
            <div class="card card-flush bg-light-primary h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-gray-700 fw-semibold fs-6 d-block">Total</span>
                        <span class="text-gray-900 fw-bold fs-2">{{ $stats['total_comprovantes'] }}</span>
                    </div>
                    <i class="ki-duotone ki-file-added fs-3x text-primary">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush bg-light-success h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-gray-700 fw-semibold fs-6 d-block">Processados</span>
                        <span class="text-gray-900 fw-bold fs-2">{{ $stats['processados'] }}</span>
                    </div>
                    <i class="ki-duotone ki-check-circle fs-3x text-success">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush bg-light-warning h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-gray-700 fw-semibold fs-6 d-block">Revisao Manual</span>
                        <span class="text-gray-900 fw-bold fs-2">{{ $stats['revisao_manual'] }}</span>
                    </div>
                    <i class="ki-duotone ki-eye fs-3x text-warning">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush bg-light-danger h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-gray-700 fw-semibold fs-6 d-block">Erros</span>
                        <span class="text-gray-900 fw-bold fs-2">{{ $stats['erros'] }}</span>
                    </div>
                    <i class="ki-duotone ki-cross-circle fs-3x text-danger">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
        </div>
    </div>

    {{-- How it Works --}}
    <div class="card card-flush mb-5">
        <div class="card-header pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-900">Como Funciona</span>
                <span class="text-gray-500 mt-1 fw-semibold fs-6">Fluxo de processamento de comprovantes com IA</span>
            </h3>
        </div>
        <div class="card-body">
            {{-- Flow Diagram --}}
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 mb-10">
                <div class="text-center">
                    <div class="symbol symbol-60px mb-2">
                        <span class="symbol-label bg-light-primary">
                            <i class="ki-duotone ki-file-up fs-2x text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <span class="text-gray-800 fw-semibold d-block fs-7">1. Upload</span>
                    <span class="text-gray-500 fs-8">PDF/Imagem</span>
                </div>
                <i class="ki-duotone ki-arrow-right fs-2x text-gray-400"></i>
                <div class="text-center">
                    <div class="symbol symbol-60px mb-2">
                        <span class="symbol-label bg-light-info">
                            <i class="ki-duotone ki-scan-barcode fs-2x text-info">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                                <span class="path7"></span>
                                <span class="path8"></span>
                            </i>
                        </span>
                    </div>
                    <span class="text-gray-800 fw-semibold d-block fs-7">2. OCR</span>
                    <span class="text-gray-500 fs-8">Extrai Texto</span>
                </div>
                <i class="ki-duotone ki-arrow-right fs-2x text-gray-400"></i>
                <div class="text-center">
                    <div class="symbol symbol-60px mb-2">
                        <span class="symbol-label bg-light-warning">
                            <i class="ki-duotone ki-technology-4 fs-2x text-warning">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                                <span class="path7"></span>
                            </i>
                        </span>
                    </div>
                    <span class="text-gray-800 fw-semibold d-block fs-7">3. IA (GPT-4o)</span>
                    <span class="text-gray-500 fs-8">Analisa Dados</span>
                </div>
                <i class="ki-duotone ki-arrow-right fs-2x text-gray-400"></i>
                <div class="text-center">
                    <div class="symbol symbol-60px mb-2">
                        <span class="symbol-label bg-light-success">
                            <i class="ki-duotone ki-check-square fs-2x text-success">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <span class="text-gray-800 fw-semibold d-block fs-7">4. Valida</span>
                    <span class="text-gray-500 fs-8">Normaliza</span>
                </div>
                <i class="ki-duotone ki-arrow-right fs-2x text-gray-400"></i>
                <div class="text-center">
                    <div class="symbol symbol-60px mb-2">
                        <span class="symbol-label bg-light-primary">
                            <i class="ki-duotone ki-data fs-2x text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                        </span>
                    </div>
                    <span class="text-gray-800 fw-semibold d-block fs-7">5. Salva</span>
                    <span class="text-gray-500 fs-8">Banco de Dados</span>
                </div>
            </div>

            {{-- Description --}}
            <div class="row">
                <div class="col-md-6">
                    <h4 class="fw-bold text-gray-900 mb-3">Campos Extraidos Automaticamente</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge badge-light-primary">Data Pagamento</span>
                        <span class="badge badge-light-primary">Valor</span>
                        <span class="badge badge-light-primary">Pagador</span>
                        <span class="badge badge-light-primary">Favorecido</span>
                        <span class="badge badge-light-primary">CPF/CNPJ</span>
                        <span class="badge badge-light-primary">Forma Pagamento</span>
                        <span class="badge badge-light-primary">Banco Origem</span>
                        <span class="badge badge-light-primary">Banco Destino</span>
                        <span class="badge badge-light-primary">Chave PIX</span>
                        <span class="badge badge-light-primary">ID Transacao</span>
                        <span class="badge badge-light-primary">Autenticacao</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <h4 class="fw-bold text-gray-900 mb-3">Tipos Suportados</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge badge-light-success">PIX</span>
                        <span class="badge badge-light-success">TED</span>
                        <span class="badge badge-light-success">DOC</span>
                        <span class="badge badge-light-success">Boleto</span>
                        <span class="badge badge-light-success">Cartao Credito</span>
                        <span class="badge badge-light-success">Cartao Debito</span>
                        <span class="badge badge-light-info">PDF</span>
                        <span class="badge badge-light-info">JPG</span>
                        <span class="badge badge-light-info">PNG</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Configuration --}}
    <div class="row g-5 mb-5">
        {{-- Webhook Info --}}
        <div class="col-md-6">
            <div class="card card-flush h-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">Webhook URL</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Endpoint para enviar comprovantes</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="bg-light rounded p-4 mb-4">
                        <code class="text-gray-800 fs-6">POST http://localhost:5680/webhook/comprovante</code>
                    </div>

                    <h5 class="fw-bold text-gray-900 mb-3">Exemplo de Requisicao</h5>
                    <div class="bg-dark rounded p-4">
<pre class="text-white mb-0"><code>{
  "data_pagamento": "2025-11-28",
  "valor": 1500.00,
  "pagador": "Joao da Silva",
  "favorecido": "Empresa ABC",
  "forma_pagamento": "PIX",
  "file_base64": "base64_do_arquivo...",
  "file_name": "comprovante.png",
  "file_type": "png"
}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- API Token --}}
        <div class="col-md-6">
            <div class="card card-flush h-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">Configuracao API</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Credenciais de acesso</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <label class="form-label fw-semibold">API Token</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="apiToken" value="{{ config('services.n8n.api_token', env('N8N_API_TOKEN', '')) }}" readonly>
                            <button class="btn btn-light-primary" type="button" onclick="toggleToken()">
                                <i class="ki-duotone ki-eye fs-4" id="eyeIcon">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </button>
                            <button class="btn btn-light-success" type="button" onclick="copyToken()">
                                <i class="ki-duotone ki-copy fs-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </button>
                        </div>
                        <div class="form-text">Use este token no header <code>X-API-Token</code></div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-semibold">Endpoint API</label>
                        <input type="text" class="form-control" value="{{ url('/api/v1/comprovantes') }}" readonly>
                    </div>

                    <div class="separator my-5"></div>

                    <h5 class="fw-bold text-gray-900 mb-3">Headers Obrigatorios</h5>
                    <div class="table-responsive">
                        <table class="table table-row-bordered">
                            <tbody>
                                <tr>
                                    <td class="text-gray-800 fw-semibold">Content-Type</td>
                                    <td><code>application/json</code></td>
                                </tr>
                                <tr>
                                    <td class="text-gray-800 fw-semibold">Accept</td>
                                    <td><code>application/json</code></td>
                                </tr>
                                <tr>
                                    <td class="text-gray-800 fw-semibold">X-API-Token</td>
                                    <td><code>seu_token_aqui</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Test Section --}}
    <div class="card card-flush">
        <div class="card-header pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-900">Testar Integracao</span>
                <span class="text-gray-500 mt-1 fw-semibold fs-6">Envie um comprovante de teste</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <form id="testForm">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Data Pagamento</label>
                                <input type="date" class="form-control" name="data_pagamento" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Valor</label>
                                <input type="number" step="0.01" class="form-control" name="valor" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pagador</label>
                                <input type="text" class="form-control" name="pagador" placeholder="Nome do pagador">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Favorecido</label>
                                <input type="text" class="form-control" name="favorecido" placeholder="Nome do favorecido">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Forma de Pagamento</label>
                                <select class="form-select" name="forma_pagamento">
                                    <option value="PIX">PIX</option>
                                    <option value="TED">TED</option>
                                    <option value="DOC">DOC</option>
                                    <option value="Boleto">Boleto</option>
                                    <option value="Cartao de Credito">Cartao de Credito</option>
                                    <option value="Cartao de Debito">Cartao de Debito</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">ID Transacao</label>
                                <input type="text" class="form-control" name="identificador_transacao" placeholder="Opcional">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-duotone ki-send fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Enviar Teste
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="bg-light rounded p-4 h-100">
                        <h5 class="fw-bold text-gray-900 mb-3">Resultado</h5>
                        <pre id="testResult" class="text-gray-700 mb-0 fs-7" style="white-space: pre-wrap;">Aguardando teste...</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleToken() {
            const input = document.getElementById('apiToken');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }

        function copyToken() {
            const input = document.getElementById('apiToken');
            input.type = 'text';
            input.select();
            document.execCommand('copy');
            input.type = 'password';

            Swal.fire({
                icon: 'success',
                title: 'Token copiado!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        }

        document.getElementById('testForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            data.valor = parseFloat(data.valor) || 0;
            data.origem = 'teste_manual';

            const resultEl = document.getElementById('testResult');
            resultEl.textContent = 'Enviando...';

            try {
                const response = await fetch('/api/v1/comprovantes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-API-Token': '{{ config('services.n8n.api_token', env('N8N_API_TOKEN', '')) }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                resultEl.textContent = JSON.stringify(result, null, 2);

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Comprovante criado com ID: ' + result.data.id,
                        timer: 3000
                    });
                }
            } catch (error) {
                resultEl.textContent = 'Erro: ' + error.message;
            }
        });
    </script>
    @endpush
</x-layouts.app>
