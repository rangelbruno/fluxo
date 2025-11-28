<x-layouts.app>
    {{-- Toolbar --}}
    <div class="d-flex flex-wrap flex-stack my-5">
        <h2 class="fs-2 fw-semibold my-2">
            Comprovantes
            <span class="fs-6 text-gray-500 ms-1">Processamento com IA</span>
        </h2>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="ki-duotone ki-file-up fs-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Enviar Comprovante
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-5 mb-5">
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body d-flex align-items-center py-5">
                    <div class="symbol symbol-50px me-3">
                        <span class="symbol-label bg-light-primary">
                            <i class="ki-duotone ki-file-added fs-2x text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-800 fw-bold fs-2">{{ $stats['total'] }}</span>
                        <span class="text-gray-500 fw-semibold d-block fs-7">Total</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body d-flex align-items-center py-5">
                    <div class="symbol symbol-50px me-3">
                        <span class="symbol-label bg-light-success">
                            <i class="ki-duotone ki-check-circle fs-2x text-success">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-800 fw-bold fs-2">{{ $stats['processados'] }}</span>
                        <span class="text-gray-500 fw-semibold d-block fs-7">Processados</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body d-flex align-items-center py-5">
                    <div class="symbol symbol-50px me-3">
                        <span class="symbol-label bg-light-warning">
                            <i class="ki-duotone ki-time fs-2x text-warning">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-800 fw-bold fs-2">{{ $stats['pendentes'] }}</span>
                        <span class="text-gray-500 fw-semibold d-block fs-7">Pendentes</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body d-flex align-items-center py-5">
                    <div class="symbol symbol-50px me-3">
                        <span class="symbol-label bg-light-danger">
                            <i class="ki-duotone ki-cross-circle fs-2x text-danger">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-800 fw-bold fs-2">{{ $stats['erros'] }}</span>
                        <span class="text-gray-500 fw-semibold d-block fs-7">Erros</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card card-flush mb-5">
        <div class="card-body py-4">
            <form method="GET" action="{{ route('comprovantes.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fs-7 fw-semibold">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="processado" {{ request('status') == 'processado' ? 'selected' : '' }}>Processado</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="erro" {{ request('status') == 'erro' ? 'selected' : '' }}>Erro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fs-7 fw-semibold">Forma de Pagamento</label>
                    <select name="forma_pagamento" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <option value="PIX" {{ request('forma_pagamento') == 'PIX' ? 'selected' : '' }}>PIX</option>
                        <option value="TED" {{ request('forma_pagamento') == 'TED' ? 'selected' : '' }}>TED</option>
                        <option value="DOC" {{ request('forma_pagamento') == 'DOC' ? 'selected' : '' }}>DOC</option>
                        <option value="Boleto" {{ request('forma_pagamento') == 'Boleto' ? 'selected' : '' }}>Boleto</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-7 fw-semibold">Data Inicio</label>
                    <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-7 fw-semibold">Data Fim</label>
                    <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-light-primary w-100">
                        <i class="ki-duotone ki-magnifier fs-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card card-flush">
        <div class="card-header pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-900">Lista de Comprovantes</span>
                <span class="text-gray-500 mt-1 fw-semibold fs-7">{{ $comprovantes->total() }} registros encontrados</span>
            </h3>
            <div class="card-toolbar">
                <form method="GET" action="{{ route('comprovantes.index') }}" class="d-flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm w-200px" placeholder="Buscar...">
                    <button type="submit" class="btn btn-sm btn-icon btn-light-primary">
                        <i class="ki-duotone ki-magnifier fs-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted">
                            <th class="min-w-100px">Data</th>
                            <th class="min-w-150px">Pagador</th>
                            <th class="min-w-150px">Favorecido</th>
                            <th class="min-w-100px">Valor</th>
                            <th class="min-w-80px">Tipo</th>
                            <th class="min-w-80px">Status</th>
                            <th class="min-w-80px text-end">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comprovantes as $comprovante)
                            <tr>
                                <td>
                                    <span class="text-gray-800 fw-bold d-block fs-7">
                                        {{ $comprovante->data_pagamento ? \Carbon\Carbon::parse($comprovante->data_pagamento)->format('d/m/Y') : '-' }}
                                    </span>
                                    <span class="text-muted fw-semibold d-block fs-8">
                                        {{ $comprovante->created_at->format('H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-gray-800 fw-bold d-block fs-7">{{ $comprovante->pagador ?? '-' }}</span>
                                    <span class="text-muted fw-semibold d-block fs-8">{{ $comprovante->banco_origem ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-800 fw-bold d-block fs-7">{{ $comprovante->favorecido ?? '-' }}</span>
                                    <span class="text-muted fw-semibold d-block fs-8">{{ $comprovante->banco_destino ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-800 fw-bold fs-6">
                                        R$ {{ $comprovante->valor ? number_format($comprovante->valor, 2, ',', '.') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($comprovante->forma_pagamento)
                                        <span class="badge badge-light-primary">{{ $comprovante->forma_pagamento }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($comprovante->status)
                                        @case('processado')
                                            <span class="badge badge-light-success">Processado</span>
                                            @break
                                        @case('pendente')
                                            <span class="badge badge-light-warning">Pendente</span>
                                            @break
                                        @case('erro')
                                            <span class="badge badge-light-danger">Erro</span>
                                            @break
                                        @default
                                            <span class="badge badge-light">{{ $comprovante->status }}</span>
                                    @endswitch
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-icon btn-light-primary" onclick="showDetails({{ $comprovante->id }})" title="Ver detalhes">
                                        <i class="ki-duotone ki-eye fs-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger" onclick="deleteComprovante({{ $comprovante->id }})" title="Excluir">
                                        <i class="ki-duotone ki-trash fs-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ki-duotone ki-file-added fs-3x text-gray-400 mb-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="text-gray-500 fs-6">Nenhum comprovante encontrado</span>
                                        <span class="text-gray-400 fs-7">Clique em "Enviar Comprovante" para adicionar</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($comprovantes->hasPages())
                <div class="d-flex justify-content-end mt-5">
                    {{ $comprovantes->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Upload Modal --}}
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Enviar Comprovante</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info d-flex align-items-center mb-5">
                            <i class="ki-duotone ki-information-2 fs-2x text-info me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div>
                                <span class="fw-bold">Processamento com IA</span>
                                <p class="mb-0 fs-7">O comprovante sera analisado automaticamente para extrair data, valor, pagador, favorecido e tipo de pagamento.</p>
                            </div>
                        </div>

                        {{-- Tabs --}}
                        <ul class="nav nav-tabs nav-line-tabs mb-5" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab_arquivo" role="tab">
                                    <i class="ki-duotone ki-file-up fs-4 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Upload de Arquivo
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab_texto" role="tab">
                                    <i class="ki-duotone ki-message-text-2 fs-4 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Colar Texto
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            {{-- Tab Arquivo --}}
                            <div class="tab-pane fade show active" id="tab_arquivo" role="tabpanel">
                                <div class="dropzone" id="dropzone">
                                    <div class="dz-message needsclick">
                                        <i class="ki-duotone ki-file-up fs-3x text-primary mb-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="ms-4">
                                            <h3 class="fs-5 fw-bold text-gray-900 mb-1">Arraste o arquivo aqui ou clique para selecionar</h3>
                                            <span class="fs-7 text-gray-500">Formatos aceitos: PDF, JPG, PNG (max 10MB)</span>
                                        </div>
                                    </div>
                                </div>
                                <input type="file" name="arquivo" id="arquivo" class="d-none" accept=".pdf,.jpg,.jpeg,.png">
                                <div id="filePreview" class="d-none mt-3 p-3 bg-light rounded">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-file fs-2x text-primary me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="flex-grow-1">
                                            <span id="fileName" class="fw-bold text-gray-800"></span>
                                            <span id="fileSize" class="text-muted d-block fs-7"></span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-icon btn-light-danger" onclick="clearFile()">
                                            <i class="ki-duotone ki-cross fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab Texto --}}
                            <div class="tab-pane fade" id="tab_texto" role="tabpanel">
                                <textarea name="texto" id="texto" class="form-control" rows="8" placeholder="Cole aqui o texto do comprovante..."></textarea>
                                <span class="text-muted fs-7 mt-2 d-block">Copie e cole o texto do comprovante de pagamento para analise</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnSubmit" class="btn btn-primary">
                            <span class="indicator-label">
                                <i class="ki-duotone ki-send fs-4 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Processar
                            </span>
                            <span class="indicator-progress">
                                Processando... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Details Modal --}}
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Detalhes do Comprovante</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <div class="d-flex justify-content-center py-10">
                        <span class="spinner-border text-primary"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Dropzone functionality
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('arquivo');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');

        dropzone.addEventListener('click', () => fileInput.click());
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('bg-light-primary');
        });
        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('bg-light-primary');
        });
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('bg-light-primary');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                showFilePreview(e.dataTransfer.files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                showFilePreview(e.target.files[0]);
            }
        });

        function showFilePreview(file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            filePreview.classList.remove('d-none');
            dropzone.classList.add('d-none');
        }

        function clearFile() {
            fileInput.value = '';
            filePreview.classList.add('d-none');
            dropzone.classList.remove('d-none');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Form submission
        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn = document.getElementById('btnSubmit');
            btn.setAttribute('data-kt-indicator', 'on');
            btn.disabled = true;

            const formData = new FormData(this);

            try {
                const response = await fetch('{{ route('comprovantes.processar') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert('Sucesso! ' + result.message);
                    window.location.reload();
                } else {
                    alert('Erro: ' + (result.message || 'Erro ao processar comprovante'));
                }
            } catch (error) {
                alert('Erro de conexao. Tente novamente.');
            } finally {
                btn.removeAttribute('data-kt-indicator');
                btn.disabled = false;
            }
        });

        // Show details - global function
        window.showDetails = async function(id) {
            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            modal.show();

            try {
                const response = await fetch(`/comprovantes/${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    document.getElementById('detailsContent').innerHTML = `
                        <div class="row g-5">
                            <div class="col-md-6">
                                <div class="fw-bold text-gray-600 fs-7">Data do Pagamento</div>
                                <div class="fs-6 text-gray-800">${data.data_pagamento ? new Date(data.data_pagamento).toLocaleDateString('pt-BR') : '-'}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-gray-600 fs-7">Valor</div>
                                <div class="fs-6 text-gray-800 fw-bold">${data.valor ? 'R$ ' + parseFloat(data.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2}) : '-'}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-gray-600 fs-7">Pagador</div>
                                <div class="fs-6 text-gray-800">${data.pagador || '-'}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-gray-600 fs-7">Favorecido</div>
                                <div class="fs-6 text-gray-800">${data.favorecido || '-'}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-gray-600 fs-7">Banco Origem</div>
                                <div class="fs-6 text-gray-800">${data.banco_origem || '-'}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-gray-600 fs-7">Banco Destino</div>
                                <div class="fs-6 text-gray-800">${data.banco_destino || '-'}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-gray-600 fs-7">Forma de Pagamento</div>
                                <div class="fs-6"><span class="badge badge-light-primary">${data.forma_pagamento || '-'}</span></div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-gray-600 fs-7">Identificador</div>
                                <div class="fs-7 text-gray-800 font-monospace">${data.identificador_transacao || '-'}</div>
                            </div>
                            <div class="col-12">
                                <div class="separator my-3"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold text-gray-600 fs-7">Status</div>
                                <div class="fs-6">
                                    ${data.status === 'processado' ? '<span class="badge badge-light-success">Processado</span>' :
                                      data.status === 'pendente' ? '<span class="badge badge-light-warning">Pendente</span>' :
                                      '<span class="badge badge-light-danger">Erro</span>'}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold text-gray-600 fs-7">Confianca IA</div>
                                <div class="fs-6">${data.confianca_extracao || 0}%</div>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold text-gray-600 fs-7">Origem</div>
                                <div class="fs-6"><span class="badge badge-light">${data.origem || 'web'}</span></div>
                            </div>
                        </div>
                    `;
                }
            } catch (error) {
                document.getElementById('detailsContent').innerHTML = '<div class="text-center text-danger py-5">Erro ao carregar detalhes</div>';
            }
        }

        // Delete comprovante - global function
        window.deleteComprovante = async function(id) {
            if (!confirm('Confirmar exclusao? Esta acao nao pode ser desfeita.')) {
                return;
            }

            try {
                const response = await fetch(`/comprovantes/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Excluido com sucesso!');
                    window.location.reload();
                } else {
                    alert('Erro: ' + (data.message || 'Erro ao excluir'));
                }
            } catch (error) {
                alert('Erro de conexao');
            }
        }
    </script>

    <style>
        .dropzone {
            border: 2px dashed #e4e6ef;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .dropzone:hover {
            border-color: #009ef7;
            background-color: #f1faff;
        }
    </style>
    @endpush
</x-layouts.app>
