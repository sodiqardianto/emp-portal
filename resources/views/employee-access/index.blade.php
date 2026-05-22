@extends('layouts.app')

@section('title', 'Hak Akses - Employee Portal')

@section('content')
    <div class="container-xxl px-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="page-title mb-1">Hak Akses</h1>
                <nav aria-label="breadcrumb">
                    <ol class="page-breadcrumb">
                        <li class="page-breadcrumb-item"><a href="{{ route('home') }}"><i class="fa-solid fa-house-chimney"></i> Home</a></li>
                        <li class="page-breadcrumb-item">System</li>
                        <li class="page-breadcrumb-item active" aria-current="page">Hak Akses</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if (session('success'))
            <div class="app-alert app-alert--success mb-4">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="app-card">
            <div class="app-card-header">
                <div>
                    <h2 class="app-card-title">Daftar Karyawan</h2>
                    <p class="app-card-subtitle">Kelola hak akses menu untuk setiap karyawan</p>
                </div>
            </div>

            <div class="app-card-body p-0">
                <div id="app-table"
                     data-url="{{ route('employee-access.data') }}"
                     data-can-edit="{{ $canEdit ? '1' : '0' }}"
                     data-can-delete="{{ $canDelete ? '1' : '0' }}"
                     data-edit-url="{{ route('employee-access.edit', ['employeeCode' => '__CODE__']) }}"
                     data-delete-url="{{ route('employee-access.destroy', ['employeeCode' => '__CODE__']) }}">

                    <div class="app-table-wrap">
                        <table class="app-table">
                            <thead>
                                <tr class="app-table-head">
                                    <th class="app-table-th sortable" data-field="EmployeeCode" style="min-width:130px">Code <i class="fa-solid fa-sort sort-icon"></i></th>
                                    <th class="app-table-th sortable" data-field="EmployeeName" style="min-width:180px">Nama <i class="fa-solid fa-sort sort-icon"></i></th>
                                    <th class="app-table-th sortable" data-field="DivName" style="min-width:140px">Divisi <i class="fa-solid fa-sort sort-icon"></i></th>
                                    <th class="app-table-th sortable" data-field="DeptName" style="min-width:140px">Department <i class="fa-solid fa-sort sort-icon"></i></th>
                                    <th class="app-table-th sortable" data-field="UnitName" style="min-width:140px">Unit <i class="fa-solid fa-sort sort-icon"></i></th>
                                    <th class="app-table-th text-end" style="min-width:100px">Actions</th>
                                </tr>
                                <tr class="app-table-filters">
                                    <th><input type="text" class="app-table-filter" data-field="EmployeeCode" placeholder="Cari code..."></th>
                                    <th><input type="text" class="app-table-filter" data-field="EmployeeName" placeholder="Cari nama..."></th>
                                    <th><input type="text" class="app-table-filter" data-field="DivName" placeholder="Cari divisi..."></th>
                                    <th><input type="text" class="app-table-filter" data-field="DeptName" placeholder="Cari dept..."></th>
                                    <th><input type="text" class="app-table-filter" data-field="UnitName" placeholder="Cari unit..."></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="6" class="app-table-loading"><i class="fa-solid fa-spinner fa-spin"></i> Memuat data...</td></tr>
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
                            <h5 class="fw-bold mb-2" style="color:#0f172a;">Hapus Hak Akses</h5>
                            <p class="text-muted mb-0">Semua hak akses untuk <strong class="delete-name" style="color:#0f172a;"></strong> akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
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
    @vite(['resources/views/employee-access/employee-access-table.js'])
@endpush
