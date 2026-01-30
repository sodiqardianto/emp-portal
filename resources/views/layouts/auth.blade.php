<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    @stack('styles')
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    @vite(['resources/css/custom.css'])
    <style>
        .auth-card-bg {
            background-color: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body id="kt_body" class="auth-bg bgi-size-cover bgi-attachment-fixed bgi-position-center">
    <script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>

    <div class="d-flex flex-column flex-root">
        <style>
            body { background-image: url('{{ asset('assets/media/illustrations/auth/bg10.jpeg') }}'); }
            [data-bs-theme="dark"] body { background-image: url('{{ asset('assets/media/illustrations/auth/bg10-dark.jpeg') }}'); }
        </style>

        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <div class="d-flex flex-lg-row-fluid">
                <div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
                    <img class="theme-light-show mx-auto mw-100 w-150px w-lg-700px mb-10 mb-lg-20" src="{{ asset('assets/media/logos/Logo_Bethsaida_Hospitals.png') }}" alt="" />
                    <img class="theme-dark-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="{{ asset('assets/media/illustrations/auth/agency-dark.png') }}" alt="" />
                    <h1 class="text-gray-800 fs-2qx fw-bold text-center mb-7">Bethsaida Hospitals Medical Check Up</h1>
                    <div class="text-gray-600 fs-base text-center fw-semibold">Manage patient data, examination schedules, and MCU results
                    <br />easily and efficiently. An integrated system to
                    <br />support better healthcare services.</div>
                </div>
            </div>

            <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
                <div class="auth-card-bg d-flex flex-column flex-center rounded-4 w-md-600px p-10">
                    <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">
                        <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>var hostUrl = "{{ asset('assets') }}/";</script>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    @stack('scripts')
</body>
</html>
