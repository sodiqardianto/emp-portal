@php
    $user = Auth::user();
    $employeeName = $user?->name ?: 'Employee';
    $employeeEmail = $user?->email ?: ($user?->EmployeeCode ?? '');
    $employeeInitial = strtoupper(substr($employeeName, 0, 1));
@endphp

<header class="app-header">
    <div class="app-header-inner">
        {{-- Sidebar toggle (works on desktop + mobile) --}}
        <div class="app-header-left">
            <button type="button"
                    class="app-header-toggle"
                    data-app-shell-toggle
                    aria-label="Toggle sidebar"
                    title="Toggle sidebar">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        {{-- Actions --}}
        <div class="app-header-actions">
            {{-- Notifications --}}
            <div class="dropdown">
                <button type="button" class="app-header-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                    <i class="fa-regular fa-bell"></i>
                    <span class="app-header-badge"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end app-header-dropdown">
                    <div class="app-header-dropdown-header">
                        <div>
                            <p class="app-header-dropdown-name mb-0">Notifikasi</p>
                            <span class="app-header-dropdown-meta">Belum ada notifikasi baru</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- User menu --}}
            <div class="dropdown">
                <button type="button" class="app-header-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="User menu">
                    <i class="fa-regular fa-user"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end app-header-dropdown">
                    <div class="app-header-dropdown-header">
                        <div class="app-header-dropdown-avatar">{{ $employeeInitial }}</div>
                        <div class="min-w-0">
                            <p class="app-header-dropdown-name">{{ $employeeName }}</p>
                            <span class="app-header-dropdown-meta">{{ $employeeEmail }}</span>
                        </div>
                    </div>
                    <a href="#" class="dropdown-item">
                        <i class="fa-solid fa-id-card"></i>
                        <span>My Profile</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Theme toggle (placeholder) --}}
            <button type="button" class="app-header-btn" aria-label="Toggle theme">
                <i class="fa-regular fa-sun"></i>
            </button>
        </div>
    </div>
</header>
