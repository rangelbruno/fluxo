<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>Dashboard - Sistema Legislativo</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet">
</head>

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled">
    <div class="d-flex flex-column flex-root">
        <div class="page d-flex flex-row flex-column-fluid">
            <!-- Sidebar -->
            <x-layouts.sidebar />

            <!-- Wrapper -->
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <!-- Header -->
            <x-layouts.header />

            <!-- Content -->
            <div class="content d-flex flex-column flex-column-fluid">
                <div class="post d-flex flex-column-fluid">
                    <div id="kt_content_container" class="container-xxl">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- User Info Card -->
                        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
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
                        </div>

                        <!-- Welcome Message -->
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
                </div>
            </div>
            </div>
            <!--end::Wrapper-->
        </div>
    </div>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
</body>
</html>