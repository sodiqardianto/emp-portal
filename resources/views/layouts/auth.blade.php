<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="login-body">
    <div class="login-wrapper">
        {{-- ========== LEFT PANEL ========== --}}
        <div class="login-left-panel">
            <div class="login-left-inner">
                {{-- Logo --}}
                <div class="login-logo">
                    <div class="login-logo-icon">
                        <img src="{{ asset('assets/media/logos/logo_only_transparant.png') }}" alt="Bethsaida" />
                    </div>
                    <div class="login-logo-text">
                        <span class="login-logo-title">Bethsaida</span>
                        <span class="login-logo-subtitle">EMPLOYEE PORTAL</span>
                    </div>
                </div>

                {{-- Hero --}}
                <div class="login-hero">
                    <div class="login-hero-badge">
                        <span class="login-hero-badge-dot"></span>
                        PORTAL KARYAWAN
                    </div>
                    <h1 class="login-hero-title">Selamat Datang,<br />Tim Bethsaida</h1>
                    <p class="login-hero-desc">
                        Akses portal eksklusif untuk karyawan Bethsaida Hospitals. Kelola jadwal, dan informasi penting
                        lainnya.
                    </p>
                </div>

                {{-- Feature Cards --}}
                <div class="login-features">
                    <div class="login-feature-card">
                        <div class="login-feature-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="login-feature-text">
                            <strong>Aman &amp; Terenkripsi</strong>
                            <span>Data karyawan terlindungi sepenuhnya</span>
                        </div>
                    </div>
                    <div class="login-feature-card">
                        <div class="login-feature-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="login-feature-text">
                            <strong>Respons Instan</strong>
                            <span>Akses cepat ke semua fitur</span>
                        </div>
                    </div>
                    <div class="login-feature-card">
                        <div class="login-feature-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="login-feature-text">
                            <strong>Tersedia 24/7</strong>
                            <span>Siap membantu kapan saja</span>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="login-left-footer">
                    &copy; {{ date('Y') }} Bethsaida Hospitals · <em>Hospital with Heart</em>
                </div>
            </div>

            {{-- Decorative elements --}}
            <div class="login-left-decor-circle login-left-decor-circle--1"></div>
            <div class="login-left-decor-circle login-left-decor-circle--2"></div>
            <div class="login-left-decor-grid"></div>
        </div>

        {{-- ========== RIGHT PANEL ========== --}}
        <div class="login-right-panel">
            {{-- Dot pattern background --}}
            <div class="login-right-dots"></div>

            {{-- Mobile logo (hidden on desktop) --}}
            <div class="login-mobile-logo">
                <div class="login-logo-icon login-logo-icon--sm">
                    <img src="{{ asset('assets/media/logos/logo_only_transparant.png') }}" alt="Bethsaida" />
                </div>
                <div class="login-logo-text">
                    <span class="login-logo-title login-logo-title--dark">Bethsaida</span>
                    <span class="login-logo-subtitle login-logo-subtitle--dark">EMPLOYEE PORTAL</span>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="login-form-card">
                @yield('content')
            </div>
        </div>
    </div>

    <script>var hostUrl = "{{ asset('assets') }}/";</script>
    @stack('scripts')
</body>

</html>