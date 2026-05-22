@extends('layouts.auth')

@section('title', 'Masuk - Employee Portal')

@section('content')
{{-- Header icon --}}
<div class="login-form-header">
    <div class="login-form-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c0 1.306.835 2.417 2 2.83" />
        </svg>
    </div>
    <h1 class="login-form-title">Masuk</h1>
    <p class="login-form-subtitle">Gunakan NIK dan password karyawan Anda</p>
</div>

{{-- Server error --}}
@if(session('error') || $errors->any())
<div class="login-alert login-alert--error">
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <p>
        @if(session('error'))
            {{ session('error') }}
        @else
            {{ $errors->first() }}
        @endif
    </p>
</div>
@endif

<form method="POST" action="{{ route('login') }}" novalidate class="login-form" id="login-form">
    @csrf

    {{-- NIK Field --}}
    <div class="login-field-group">
        <label for="nik" class="login-field-label">NIK KARYAWAN</label>
        <div class="login-input-wrapper">
            <span class="login-input-icon">
                <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c0 1.306.835 2.417 2 2.83" />
                </svg>
            </span>
            <input
                id="nik"
                type="text"
                name="username"
                value="{{ old('username') }}"
                autocomplete="username"
                placeholder="Masukkan NIK karyawan"
                class="login-input {{ $errors->has('username') ? 'login-input--error' : '' }}"
                autofocus
            />
        </div>
        @error('username')
            <p class="login-field-error">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01" />
                </svg>
                <span>{{ $message }}</span>
            </p>
        @enderror
    </div>

    {{-- Password Field --}}
    <div class="login-field-group">
        <div class="login-field-label-row">
            <label for="password" class="login-field-label">PASSWORD</label>
        </div>
        <div class="login-input-wrapper">
            <span class="login-input-icon">
                <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </span>
            <input
                id="password"
                type="password"
                name="password"
                autocomplete="current-password"
                placeholder="Masukkan password"
                class="login-input {{ $errors->has('password') ? 'login-input--error' : '' }}"
            />
            <button type="button" class="login-toggle-password" onclick="togglePassword()" tabindex="-1" aria-label="Toggle password visibility">
                <svg id="eye-open" width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg id="eye-closed" width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
            </button>
        </div>
        @error('password')
            <p class="login-field-error">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01" />
                </svg>
                <span>{{ $message }}</span>
            </p>
        @enderror
    </div>

    {{-- Submit --}}
    <div class="login-submit-wrapper">
        <button type="submit" class="login-submit-btn" id="login-submit-btn">
            <span class="login-submit-spinner" aria-hidden="true"></span>
            <span class="login-submit-label">Masuk ke Sistem</span>
            <svg class="login-submit-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        </button>
    </div>
</form>

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.style.display = 'none';
            eyeClosed.style.display = 'block';
        } else {
            input.type = 'password';
            eyeOpen.style.display = 'block';
            eyeClosed.style.display = 'none';
        }
    }

    document.getElementById('login-form')?.addEventListener('submit', function () {
        const button = document.getElementById('login-submit-btn');
        const label = button?.querySelector('.login-submit-label');

        if (!button || button.disabled) {
            return;
        }

        button.disabled = true;
        button.classList.add('is-loading');

        if (label) {
            label.textContent = 'Memproses...';
        }
    });
</script>
@endpush
@endsection
