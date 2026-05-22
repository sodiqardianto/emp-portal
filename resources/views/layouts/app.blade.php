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
<body class="app-shell">
    <div class="app-shell-root">
        @include('layouts.partials.sidebar')

        <div class="app-sidebar-backdrop" data-app-shell-close></div>

        <div class="app-shell-main">
            @include('layouts.partials.header')

            <main class="app-shell-content">
                @yield('content')
            </main>

            @include('layouts.partials.footer')
        </div>
    </div>

    @stack('scripts')
    <button type="button" class="scroll-to-top" id="scroll-top" aria-label="Scroll to top">
        <i class="fa-solid fa-arrow-up"></i>
    </button>
    <script>
    (function(){const b=document.getElementById('scroll-top');window.addEventListener('scroll',()=>b.classList.toggle('visible',window.scrollY>300));b.addEventListener('click',()=>window.scrollTo({top:0,behavior:'smooth'}));})();
    </script>
</body>
</html>
