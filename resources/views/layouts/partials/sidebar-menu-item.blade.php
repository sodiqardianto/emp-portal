@php
    use Illuminate\Support\Str;

    $children = $item['children'] ?? [];
    $hasChildren = count($children) > 0;
    $href = $item['href'] ?? '#';
    $hrefPath = trim(parse_url($href, PHP_URL_PATH) ?? '', '/');
    $isCurrent = $hrefPath !== '' && request()->is($hrefPath);

    $containsActive = function (array $items) use (&$containsActive): bool {
        foreach ($items as $child) {
            $childPath = trim(parse_url($child['href'] ?? '#', PHP_URL_PATH) ?? '', '/');

            if ($childPath !== '' && request()->is($childPath)) {
                return true;
            }

            if ($containsActive($child['children'] ?? [])) {
                return true;
            }
        }

        return false;
    };

    $isOpen = $hasChildren && $containsActive($children);
    $depth = (int) ($item['depth'] ?? 1);

    // Resolve icon markup based on the raw value from DB (tbl_menu.icon).
    // Priority:
    //   1. Raw FontAwesome (fa, fas, far, fab, fa-solid ...) -> gunakan apa adanya
    //   2. Bootstrap Icons (bi-...) -> pakai class "bi bi-..."
    //   3. Legacy Metronic (ki-...) -> map ke FontAwesome terdekat, else default folder
    //   4. Kosong -> default FA icon
    $legacyKiMap = [
        'ki-element-11' => 'fa-solid fa-grip',
        'ki-element-12' => 'fa-solid fa-table-cells-large',
        'ki-setting-2'  => 'fa-solid fa-gear',
        'ki-setting-3'  => 'fa-solid fa-sliders',
        'ki-shield-tick' => 'fa-solid fa-user-shield',
        'ki-shield' => 'fa-solid fa-shield-halved',
        'ki-user' => 'fa-solid fa-user',
        'ki-people' => 'fa-solid fa-users',
        'ki-category' => 'fa-solid fa-layer-group',
        'ki-menu' => 'fa-solid fa-bars',
        'ki-lock' => 'fa-solid fa-lock',
        'ki-key' => 'fa-solid fa-key',
        'ki-notification-bing' => 'fa-solid fa-bell',
        'ki-abstract-14' => 'fa-solid fa-grip-lines',
        'ki-abstract-28' => 'fa-solid fa-layer-group',
        'ki-home' => 'fa-solid fa-house',
        'ki-document' => 'fa-solid fa-file-lines',
        'ki-chart-simple' => 'fa-solid fa-chart-column',
        'ki-wrench' => 'fa-solid fa-wrench',
        'ki-calendar' => 'fa-solid fa-calendar',
    ];

    $raw = trim((string) ($item['icon'] ?? ''));
    $iconClass = null;

    if ($raw !== '') {
        $first = Str::before($raw, ' ');

        if (Str::startsWith($first, ['fa-', 'fas', 'far', 'fab', 'fal', 'fad'])) {
            // Already a FontAwesome class string
            $iconClass = $raw;
        } elseif (Str::startsWith($first, 'bi-')) {
            $iconClass = 'bi '.$raw;
        } elseif (Str::startsWith($first, 'ki-') && isset($legacyKiMap[$first])) {
            $iconClass = $legacyKiMap[$first];
        }
    }

    if ($iconClass === null) {
        // Fallback berbeda antara parent dan child
        $iconClass = $depth <= 1
            ? 'fa-solid fa-folder'
            : 'fa-regular fa-circle-dot';
    }
@endphp

@if ($hasChildren)
    <li class="app-menu-item" aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
        <button type="button" class="app-menu-link" data-app-menu-toggle>
            <span class="app-menu-icon"><i class="{{ $iconClass }}"></i></span>
            <span class="app-menu-title">{{ $item['label'] }}</span>
            <span class="app-menu-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </button>
        <ul class="app-menu-sub">
            @foreach ($children as $child)
                @include('layouts.partials.sidebar-menu-item', ['item' => $child])
            @endforeach
        </ul>
    </li>
@else
    <li class="app-menu-item">
        <a class="app-menu-link {{ $isCurrent ? 'active' : '' }}" href="{{ $href }}">
            <span class="app-menu-icon"><i class="{{ $iconClass }}"></i></span>
            <span class="app-menu-title">{{ $item['label'] }}</span>
        </a>
    </li>
@endif
