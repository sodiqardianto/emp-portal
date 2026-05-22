@extends('layouts.app')

@section('title', 'Permissions - Employee Portal')

@section('content')
    <div class="container-xxl px-0">
        {{-- Page header --}}
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="page-title mb-1">Permissions</h1>
                <nav aria-label="breadcrumb">
                    <ol class="page-breadcrumb">
                        <li class="page-breadcrumb-item">
                            <a href="{{ route('home') }}"><i class="fa-solid fa-house-chimney"></i> Home</a>
                        </li>
                        <li class="page-breadcrumb-item">System</li>
                        <li class="page-breadcrumb-item active" aria-current="page">Permissions</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="app-alert app-alert--success mb-4">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="app-alert app-alert--danger mb-4">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>
                    <div class="fw-semibold mb-1">Data belum bisa disimpan</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Data card --}}
        <div class="app-card">
            <div class="app-card-header">
                <div>
                    <h2 class="app-card-title">Daftar Permission</h2>
                    <p class="app-card-subtitle">
                        Kelola master permission untuk akses menu Employee Portal
                    </p>
                </div>
                @if ($canAdd)
                    <div class="app-card-actions">
                        <button type="button" class="btn btn-bethsaida" data-bs-toggle="modal"
                                data-bs-target="#kt_modal_add_permission">
                            <i class="fa-solid fa-plus me-1"></i>Add Permission
                        </button>
                    </div>
                @endif
            </div>

            <div class="app-card-body">
                <div id="permissions-table" class="permissions-tabulator"
                     data-url="{{ route('permissions.data') }}"
                     data-can-edit="{{ $canEdit ? '1' : '0' }}"
                     data-can-delete="{{ $canDelete ? '1' : '0' }}"
                     data-update-url-template="{{ route('permissions.update', ['code' => '__CODE__']) }}"
                     data-delete-url-template="{{ route('permissions.destroy', ['code' => '__CODE__']) }}"
                     data-initial-sort-by="{{ $filters['sortBy'] }}"
                     data-initial-sort-dir="{{ $filters['sortDir'] }}"
                     data-page-size="{{ (int) $filters['pageSize'] }}"></div>
            </div>
        </div>

        {{-- Add Modal --}}
        @if ($canAdd)
            <div class="modal fade" id="kt_modal_add_permission" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content app-modal">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fa-solid fa-shield-halved me-2"></i>Add Permission
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('permissions.store') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Permission Code <span class="text-danger">*</span></label>
                                    <input type="text" name="permission_code" value="{{ old('permission_code') }}"
                                           class="form-control text-uppercase" placeholder="VIEW" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Permission Name <span class="text-danger">*</span></label>
                                    <input type="text" name="permission_name" value="{{ old('permission_name') }}"
                                           class="form-control" placeholder="View" required />
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="3"
                                              placeholder="Melihat data/menu">{{ old('description') }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light-bethsaida" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-bethsaida">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Edit Modal --}}
        @if ($canEdit)
            <div class="modal fade" id="kt_modal_edit_permission" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content app-modal">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fa-solid fa-pen-to-square me-2"></i>Edit Permission
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="#" id="kt_modal_edit_permission_form">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Permission Code</label>
                                    <input type="text" name="permission_code"
                                           class="form-control text-uppercase" readonly />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Permission Name <span class="text-danger">*</span></label>
                                    <input type="text" name="permission_name" class="form-control" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="row g-3 mb-0">
                                    <div class="col-sm-6">
                                        <label class="form-label fw-semibold">Sort Order</label>
                                        <input type="number" min="1" name="sort_order" class="form-control" />
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label fw-semibold">Status</label>
                                        <select name="is_active" class="form-select">
                                            <option value="Y">Aktif</option>
                                            <option value="N">Nonaktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light-bethsaida" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-bethsaida">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if ($canDelete)
            <form method="POST" action="#" id="permissions-delete-form" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/permissions-table.js'])
@endpush
