@extends('layouts.app')

@section('title', 'Edit Hak Akses - ' . $employee->EmployeeName)

@section('content')
    <div class="container-xxl px-0">
        {{-- Page header --}}
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <a href="{{ route('employee-access.index') }}" class="text-muted text-decoration-none d-inline-flex align-items-center gap-1 mb-2" style="font-size:13px;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar hak akses
                </a>
                <h1 class="page-title mb-1">Ubah Hak Akses</h1>
                <nav aria-label="breadcrumb">
                    <ol class="page-breadcrumb">
                        <li class="page-breadcrumb-item"><a href="{{ route('home') }}"><i class="fa-solid fa-house-chimney"></i> Home</a></li>
                        <li class="page-breadcrumb-item">System</li>
                        <li class="page-breadcrumb-item"><a href="{{ route('employee-access.index') }}">Hak Akses</a></li>
                        <li class="page-breadcrumb-item active" aria-current="page">{{ $employee->EmployeeCode }}</li>
                    </ol>
                </nav>
            </div>
            @if ($canEdit)
                <button type="submit" form="form-access" class="btn btn-bethsaida">
                    <i class="fa-solid fa-floppy-disk me-1"></i>Simpan Perubahan
                </button>
            @endif
        </div>

        @if (session('success'))
            <div class="app-alert app-alert--success mb-4">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Employee info --}}
        <div class="app-card mb-4">
            <div class="app-card-body">
                <div class="row g-3">
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted" style="font-size:12px;">Karyawan</div>
                        <div class="fw-bold">{{ $employee->EmployeeName }}</div>
                        <div class="text-muted" style="font-size:13px;">{{ $employee->EmployeeCode }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted" style="font-size:12px;">Divisi</div>
                        <div class="fw-semibold">{{ $employee->DivName ?: '-' }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted" style="font-size:12px;">Department</div>
                        <div class="fw-semibold">{{ $employee->DeptName ?: '-' }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted" style="font-size:12px;">Unit</div>
                        <div class="fw-semibold">{{ $employee->UnitName ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Permission matrix --}}
        <form method="POST" action="{{ route('employee-access.update', $employee->EmployeeCode) }}" id="form-access">
            @csrf
            @method('PUT')

            <div class="app-card">
                <div class="app-card-body">
                    {{-- Search + global select all --}}
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                        <div class="position-relative" style="max-width:300px;width:100%;">
                            <i class="fa-solid fa-magnifying-glass position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;"></i>
                            <input type="text" id="menu-search" class="form-control" placeholder="Cari menu..." style="padding-left:36px;border-radius:12px;border-color:rgba(32,61,118,0.15);">
                        </div>
                        <label class="perm-card perm-card--select-all mb-0" id="global-select-all-label">
                            <input type="checkbox" class="perm-card-check" id="global-check-all">
                            <span class="perm-card-label">Pilih semua permission <span id="global-count"></span></span>
                        </label>
                    </div>

                    {{-- Menu cards --}}
                    <div class="d-flex flex-column gap-3" id="menu-list">
                        @php
                            // Build depth map
                            $menuById = $menus->keyBy(fn ($m) => trim($m->id_menu));
                            $depthMap = [];
                            foreach ($menus as $m) {
                                $id = trim($m->id_menu);
                                $depth = 0;
                                $current = $m;
                                while ($current && $current->parent_id && $depth < 5) {
                                    $parentId = trim($current->parent_id);
                                    if (!$menuById->has($parentId)) break;
                                    $current = $menuById->get($parentId);
                                    $depth++;
                                }
                                $depthMap[$id] = $depth;
                            }
                        @endphp

                        @foreach ($menus as $menu)
                            @php
                                $menuId = trim($menu->id_menu);
                                $available = $menuPermissions->get($menuId, []);
                                $granted = $grantedPermissions->get($menuId, []);
                                if (empty($available)) continue;
                                $depth = $depthMap[$menuId] ?? 0;
                            @endphp
                            <div class="access-menu-card" data-menu-name="{{ strtolower($menu->nama_menu) }}" style="margin-left: {{ $depth * 32 }}px;">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div>
                                        <div class="fw-bold" style="font-size:15px;">{{ $menu->nama_menu }}</div>
                                    </div>
                                    <label class="perm-card perm-card--select-all mb-0 menu-select-all">
                                        <input type="checkbox" class="perm-card-check menu-check-all" data-menu="{{ $menuId }}">
                                        <span class="perm-card-label">Pilih semua (<span class="menu-count">0</span>/{{ count($available) }})</span>
                                    </label>
                                </div>
                                <div class="perm-grid">
                                    @foreach ($catalog as $perm)
                                        @if (in_array($perm->permission_code, $available))
                                            <label class="perm-card {{ in_array($perm->permission_code, $granted) ? 'perm-card--active' : '' }}">
                                                <input type="checkbox"
                                                       class="perm-card-check perm-check"
                                                       name="assignments[{{ $menuId }}][]"
                                                       value="{{ $perm->permission_code }}"
                                                       data-menu="{{ $menuId }}"
                                                       {{ in_array($perm->permission_code, $granted) ? 'checked' : '' }}
                                                       {{ !$canEdit ? 'disabled' : '' }}>
                                                <div>
                                                    <span class="perm-card-name">{{ $perm->permission_name }}</span>
                                                    @if ($perm->description)
                                                        <span class="perm-card-desc">{{ $perm->description }}</span>
                                                    @endif
                                                </div>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const menuList = document.getElementById('menu-list');
    const search = document.getElementById('menu-search');
    const globalCheckAll = document.getElementById('global-check-all');
    const globalCount = document.getElementById('global-count');
    const allChecks = menuList.querySelectorAll('.perm-check');

    function updateCounts() {
        // Per-menu counts
        menuList.querySelectorAll('.access-menu-card').forEach(card => {
            const checks = card.querySelectorAll('.perm-check');
            const checked = [...checks].filter(c => c.checked).length;
            card.querySelector('.menu-count').textContent = checked;
            card.querySelector('.menu-check-all').checked = checks.length > 0 && checked === checks.length;
            checks.forEach(c => c.closest('.perm-card').classList.toggle('perm-card--active', c.checked));
        });

        // Global count
        const total = allChecks.length;
        const checkedTotal = [...allChecks].filter(c => c.checked).length;
        globalCount.textContent = `(${checkedTotal}/${total})`;
        globalCheckAll.checked = total > 0 && checkedTotal === total;
    }

    // Individual checkbox change
    menuList.addEventListener('change', (e) => {
        if (e.target.classList.contains('perm-check')) {
            updateCounts();
        }
        // Per-menu select all
        if (e.target.classList.contains('menu-check-all')) {
            const menuId = e.target.dataset.menu;
            const checks = menuList.querySelectorAll(`.perm-check[data-menu="${menuId}"]`);
            checks.forEach(c => c.checked = e.target.checked);
            updateCounts();
        }
    });

    // Global select all
    globalCheckAll?.addEventListener('change', () => {
        allChecks.forEach(c => c.checked = globalCheckAll.checked);
        menuList.querySelectorAll('.menu-check-all').forEach(c => c.checked = globalCheckAll.checked);
        updateCounts();
    });

    // Search
    search?.addEventListener('input', () => {
        const q = search.value.toLowerCase();
        menuList.querySelectorAll('.access-menu-card').forEach(card => {
            card.style.display = card.dataset.menuName.includes(q) ? '' : 'none';
        });
    });

    // Init
    updateCounts();
})();
</script>
@endpush
