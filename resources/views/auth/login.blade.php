@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<form class="form w-100" method="POST" action="{{ route('login') }}">
    @csrf
    
    <div class="text-center mb-11">
        <h1 class="text-gray-900 fw-bolder mb-3">Login</h1>
        <div class="text-gray-500 fw-semibold fs-6">Bethsaida Hospital MCU System</div>
    </div>

    <div class="fv-row mb-8">
        <input type="email" placeholder="Email" name="email" value="{{ old('email') }}" autocomplete="email" class="form-control bg-transparent @error('email') is-invalid @enderror" autofocus />
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="fv-row mb-3">
        <input type="password" placeholder="Password" name="password" autocomplete="current-password" class="form-control bg-transparent @error('password') is-invalid @enderror" />
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label text-gray-600" for="remember">Remember Me</label>
        </div>
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="link-primary">Forgot Password?</a>
        @endif
    </div>

    <div class="d-grid mb-10">
        <button type="submit" class="btn btn-bethsaida">
            <span class="indicator-label">Login</span>
        </button>
    </div>

    @if (Route::has('register'))
        <div class="text-gray-500 text-center fw-semibold fs-6">Not a Member yet? 
            <a href="{{ route('register') }}" class="link-primary">Sign up</a>
        </div>
    @endif
</form>
@endsection
