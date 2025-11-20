<x-layouts.app>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- User Info Card -->
    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-45px me-5">
                        <span class="symbol-label bg-light-primary text-primary fw-bold">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-start flex-column">
                        <span class="text-dark fw-bold text-hover-primary fs-6">{{ Auth::user()->name }}</span>
                        <span class="text-muted fw-semibold text-muted d-block fs-7">{{ Auth::user()->email }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-45px me-5">
                        <span class="symbol-label bg-light-success text-success">
                            <i class="ki-duotone ki-shield-tick fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <div class="d-flex justify-content-start flex-column">
                        <span class="text-dark fw-bold text-hover-primary fs-6">ID do Usuário</span>
                        <span class="text-muted fw-semibold text-muted d-block fs-7">#{{ Auth::user()->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-45px me-5">
                        <span class="symbol-label bg-light-warning text-warning">
                            <i class="ki-duotone ki-user-tick fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                    </div>
                    <div class="d-flex justify-content-start flex-column">
                        <span class="text-dark fw-bold text-hover-primary fs-6">Perfis</span>
                        <span class="text-muted fw-semibold text-muted d-block fs-7">
                            Sem perfis atribuídos
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-center flex-column py-10">
                    <span class="opacity-70 fs-4 fw-semibold mb-4">
                        Bem-vindo ao Sistema Legislativo!
                    </span>
                    <h1 class="fw-bolder fs-2qx text-gray-800 mb-7">
                        Olá, {{ Auth::user()->name }}!
                    </h1>
                    <div class="fs-6 fw-semibold text-gray-400 mb-7">
                        Você está conectado via API externa e pode acessar todas as funcionalidades do sistema.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
