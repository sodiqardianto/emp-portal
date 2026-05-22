<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>404 - Halaman Tidak Ditemukan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="error-page-body">
    <main class="error-page-shell">
        <section class="error-page-card" aria-labelledby="error-title">
            <div class="error-page-brand">
                <img src="{{ asset('assets/media/logos/logo_only_transparant.png') }}" alt="Bethsaida" />
                <span>Employee Portal</span>
            </div>

            <div class="error-page-code">404</div>
            <h1 id="error-title" class="error-page-title">Halaman tidak ditemukan</h1>
            <p class="error-page-description">
                Link yang Anda buka tidak tersedia, sudah dipindahkan, atau Anda tidak memiliki akses ke halaman tersebut.
            </p>

            <div class="error-page-actions">
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fa-solid fa-house me-2"></i>Kembali ke Dashboard
                </a>
                <button type="button" class="btn btn-light" onclick="history.back()">
                    <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                </button>
            </div>
        </section>
    </main>
</body>

</html>
