@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl px-0">
        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden"
                     style="border-radius: 18px; background: rgba(255,255,255,0.72); backdrop-filter: blur(14px);">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div>
                                <span class="badge rounded-pill px-3 py-2 mb-3"
                                      style="background: rgba(32, 61, 118, 0.1); color: #203D76; font-weight: 600;">
                                    <i class="fa-solid fa-hand-sparkles me-1"></i> Welcome back
                                </span>
                                <h1 class="mb-2" style="font-family: 'Playfair Display', Georgia, serif; color: #0f172a; font-weight: 700;">
                                    {{ $greeting }}, {{ $firstName }}
                                </h1>
                                <p class="text-muted mb-0">
                                    Selamat datang di Employee Portal Bethsaida. Gunakan menu di sidebar untuk mengakses fitur.
                                </p>
                            </div>
                            <div class="text-md-end">
                                <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing: 0.08em;">
                                    {{ now()->translatedFormat('l') }}
                                </div>
                                <div class="fs-5 fw-bold" style="color: #203D76;">
                                    {{ now()->translatedFormat('d F Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
