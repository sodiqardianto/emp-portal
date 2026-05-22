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
                    <p class="app-card-subtitle">Kelola master permission untuk akses menu Employee Portal</p>
                </div>
                @if ($canAdd)
                    <div class="app-card-actions">
                        <button type="button" class="btn btn-bethsaida" data-bs-toggle="modal" data-bs-target="#modal-add">
                            <i class="fa-solid fa-plus me-1"></i>Add Permission
                        </button>
                    </div>
                @endif
            </div>

            <div class="app-card-body p-0">
                <div id="app-table"
                     data-url="{{ route('permissions.data') }}"
                     data-can-edit="{{ $canEdit ? '1' : '0' }}"
                     data-can-delete="{{ $canDelete ? '1' : '0' }}"
                     data-update-url="{{ route('permissions.update', ['code' => '__CODE__']) }}"
                     data-delete-url="{{ route('permissions.destroy', ['code' => '__CODE__']) }}">

                    {{-- Table --}}
                    <div class="app-table-wrap">
                        <table class="app-table">
                            <thead>
                                <tr class="app-table-head">
                                    <th class="app-table-th sortable" data-field="permission_code" style="min-width:130px">Code <i class="fa-solid fa-sort sort-icon"></i></th>
                                    <th class="app-table-th sortable" data-field="permission_name" style="min-width:180px">Name <i class="fa-solid fa-sort sort-icon"></i></th>
                                    <th class="app-table-th" style="min-width:220px">Description</th>
                                    <th class="app-table-th sortable text-center" data-field="is_active" style="min-width:110px">Status <i class="fa-solid fa-sort sort-icon"></i></th>
                                    <th class="app-table-th text-end" style="min-width:100px">Actions</th>
                                </tr>
                                <tr class="app-table-filters">
                                    <th><input type="text" class="app-table-filter" data-field="permission_code" placeholder="Search..."></th>
                                    <th><input type="text" class="app-table-filter" data-field="permission_name" placeholder="Search..."></th>
                                    <th><input type="text" class="app-table-filter" data-field="description" placeholder="Search..."></th>
                                    <th>
                                        <select class="app-table-filter" data-field="is_active">
                                            <option value="">All</option>
                                            <option value="Y">Aktif</option>
                                            <option value="N">Nonaktif</option>
                                        </select>
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="app-table-body">
                                <tr><td colspan="5" class="app-table-loading"><i class="fa-solid fa-spinner fa-spin"></i> Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer --}}
                    <div class="app-table-footer">
                        <div class="app-table-info" data-table-info></div>
                        <div class="app-table-pager">
                            <select data-table-pagesize class="app-table-pagesize">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <div data-table-pagination class="app-table-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add Modal --}}
        @if ($canAdd)
            <div class="modal fade" id="modal-add" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content app-modal">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fa-solid fa-shield-halved me-2"></i>Add Permission</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('permissions.store') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Permission Code <span class="text-danger">*</span></label>
                                    <input type="text" name="permission_code" value="{{ old('permission_code') }}" class="form-control text-uppercase" placeholder="VIEW" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Permission Name <span class="text-danger">*</span></label>
                                    <input type="text" name="permission_name" value="{{ old('permission_name') }}" class="form-control" placeholder="View" required>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Melihat data/menu">{{ old('description') }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light-bethsaida" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-bethsaida"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Edit Modal --}}
        @if ($canEdit)
            <div class="modal fade" id="modal-edit" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content app-modal">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Permission</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="#" id="form-edit">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Permission Code</label>
                                    <input type="text" name="permission_code" class="form-control text-uppercase" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Permission Name <span class="text-danger">*</span></label>
                                    <input type="text" name="permission_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="row g-3 mb-0">
                                    <div class="col-sm-6">
                                        <label class="form-label fw-semibold">Sort Order</label>
                                        <input type="number" min="1" name="sort_order" class="form-control">
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
                                <button type="submit" class="btn btn-bethsaida"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if ($canDelete)
            <div class="modal fade" id="modal-delete" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content app-modal">
                        <div class="modal-body text-center py-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                 style="width:56px;height:56px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);">
                                <i class="fa-solid fa-trash" style="font-size:22px;color:#ef4444;"></i>
                            </div>
                            <h5 class="fw-bold mb-2" style="color:#0f172a;">Hapus Permission</h5>
                            <p class="text-muted mb-0">Yakin ingin menghapus permission <strong class="delete-code" style="color:#0f172a;"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                            <button type="button" class="btn btn-light-bethsaida" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-danger js-confirm-delete" style="border-radius:10px;padding:9px 18px;font-weight:600;">
                                <i class="fa-solid fa-trash me-1"></i>Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="#" id="form-delete" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
@endsection

@push('scripts')
    @vite(['resources/views/permissions/permissions-table.js'])
@endpush
