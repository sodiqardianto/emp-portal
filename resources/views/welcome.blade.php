<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Laravel</a>
            <div class="ms-auto">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-outline-light btn-sm">Home</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm me-2">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-light btn-sm">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="text-center">
            <h1 class="display-4 mb-4">Welcome to Laravel</h1>
            <p class="lead text-muted">Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</p>
        </div>
    </div>
</body>
</html>
