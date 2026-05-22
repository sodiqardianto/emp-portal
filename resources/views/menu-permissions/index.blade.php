@extends('layouts.app')

@section('title', 'Menu Permissions - Employee Portal')

@section('content')
    <div class="container-xxl px-0">
        {{-- Page header --}}
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="page-title mb-1">Menu Permissions</h1>
                <nav aria-label="breadcrumb">
                    <ol class="page-breadcrumb">
                        <li class="page-breadcrumb-item">
                            <a href="{{ route('home') }}"><i class="fa-solid fa-house-chimney"></i> Home</a>
                        </li>
                        <li class="page-breadcrumb-item">System</li>
                        <li class="page-breadcrumb-item active" aria-current="page">Menu Permissions</li>
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
                    @foreach ($errors->all() as $error)
                        <span>{{ $error }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Data card --}}
        <div class="app-card">
            <div class="app-card-header">
                <div>
                    <h2 class="app-card-title">Daftar Menu & Permission</h2>
                    <p class="app-card-subtitle">Kelola permission yang tersedia untuk setiap menu</p>
                </div>
            </div>

            <div class="app-card-body p-0">
                <div id="app-table"
                     data-url="{{ route('menu-permissions.data') }}"
                     data-show-url="{{ route('menu-permissions.show', ['id' => '__ID__']) }}"
                     data-can-edit="{{ $canEdit ? '1' : '0' }}"
                     data-can-delete="{{ $canDelete ? '1' : '0' }}"
                     data-update-url="{{ route('menu-permissions.update', ['id' => '__ID__']) }}"
                     data-delete-url="{{ route('menu-permissions.destroy', ['id' => '__ID__']) }}">

                    <div class="app-table-wrap">
                        <table class="app-table">
                            <thead>
                                <tr class="app-table-head">
                                    <th class="app-table-th sortable" data-field="nama_menu" style="min-width:200px">Menu <i class="fa-solid fa-sort sort-icon"></i></th>
                                    <th class="app-table-th sortable text-center" data-field="is_active" style="min-width:100px">Status <i class="fa-solid fa-sort sort-icon"></i></th>
                                    <th class="app-table-th" style="min-width:280px">Permissions</th>
                                    <th class="app-table-th text-end" style="min-width:100px">Actions</th>
                                </tr>
                                <tr class="app-table-filters">
                                    <th><input type="text" class="app-table-filter" data-field="search" placeholder="Cari menu..."></th>
                                    <th>
                                        <select class="app-table-filter" data-field="is_active">
                                            <option value="">All</option>
                                            <option value="Y">Aktif</option>
                                            <option value="N">Nonaktif</option>
                                        </select>
                                    </th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="4" class="app-table-loading"><i class="fa-solid fa-spinner fa-spin"></i> Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>

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

        {{-- Edit Modal --}}
        @if ($canEdit)
            <div class="modal fade" id="modal-edit" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content app-modal">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fa-solid fa-shield-halved me-2"></i>Edit Permission Menu</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="#" id="form-edit">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-muted">Menu</label>
                                    <div class="fw-bold" id="edit-menu-name"></div>
                                </div>
                                <div class="mb-3 d-flex align-items-start justify-content-between">
                                    <div>
                                        <div class="fw-bold" style="font-size:15px;">Permission Tersedia</div>
                                        <p class="text-muted mb-0" style="font-size:13px;">Atur permission yang tersedia untuk menu ini.</p>
                                    </div>
                                    <label class="perm-card perm-card--select-all mb-0">
                                        <input type="checkbox" class="perm-card-check" id="check-all">
                                        <span class="perm-card-label">Pilih semua <span id="check-count"></span></span>
                                    </label>
                                </div>
                                <div id="edit-permissions-list" class="perm-grid"></div>
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

        {{-- Delete Modal --}}
        @if ($canDelete)
            <div class="modal fade" id="modal-delete" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content app-modal">
                        <div class="modal-body text-center py-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                 style="width:56px;height:56px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);">
                                <i class="fa-solid fa-trash" style="font-size:22px;color:#ef4444;"></i>
                            </div>
                            <h5 class="fw-bold mb-2" style="color:#0f172a;">Hapus Permission Menu</h5>
                            <p class="text-muted mb-0">Semua permission untuk menu <strong class="delete-name" style="color:#0f172a;"></strong> akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
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
    @vite(['resources/views/menu-permissions/menu-permissions-table.js'])
@endpush
