@php
    $user = Auth::user();
    $employeeName = $user?->name ?: 'Employee';
    $employeeEmail = $user?->email ?: ($user?->EmployeeCode ?? '');
    $employeeInitial = strtoupper(substr($employeeName, 0, 1));
@endphp

<aside class="app-sidebar" id="app-sidebar">
    <div class="app-sidebar-inner">
        {{-- Brand --}}
        <div class="app-sidebar-brand">
            <img src="{{ asset('assets/media/logos/logo_only_transparant.png') }}"
                 alt="Bethsaida" class="app-sidebar-brand-logo" />
            <div class="app-sidebar-brand-copy">
                <span class="app-sidebar-brand-title">Bethsaida</span>
                <span class="app-sidebar-brand-subtitle">Employee Portal</span>
            </div>
        </div>

        {{-- Menu --}}
        <nav class="app-sidebar-menu" aria-label="Main navigation">
            <ul class="app-menu">
                @forelse ($sidebarMenus as $menu)
                    @include('layouts.partials.sidebar-menu-item', ['item' => $menu])
                @empty
                    <li class="app-menu-item">
                        <a class="app-menu-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <span class="app-menu-icon"><i class="fa-solid fa-house"></i></span>
                            <span class="app-menu-title">Dashboard</span>
                        </a>
                    </li>
                @endforelse
            </ul>
        </nav>

        {{-- User footer --}}
        <div class="app-sidebar-footer">
            <div class="app-sidebar-user">
                <div class="app-sidebar-user-avatar">{{ $employeeInitial }}</div>
                <div class="app-sidebar-user-copy">
                    <span class="app-sidebar-user-name">{{ $employeeName }}</span>
                    <span class="app-sidebar-user-meta">{{ $employeeEmail }}</span>
                </div>

                <div class="dropdown">
                    <button type="button" class="app-sidebar-user-action" data-bs-toggle="dropdown" aria-expanded="false" aria-label="User settings">
                        <i class="fa-solid fa-gear"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end app-header-dropdown">
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
            </div>
        </div>
    </div>
</aside>
