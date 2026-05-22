/**
 * App shell interactions:
 *  - Sidebar hide/show (desktop: collapse, mobile: overlay)
 *  - Collapsible menu groups (aria-expanded)
 */

const SIDEBAR_OPEN_CLASS = 'app-sidebar-open';       // mobile overlay
const SIDEBAR_COLLAPSED_CLASS = 'app-sidebar-collapsed'; // desktop collapse
const STORAGE_KEY = 'app-shell:sidebar-collapsed';
const DESKTOP_MQ = '(min-width: 992px)';

document.addEventListener('DOMContentLoaded', () => {
    restoreSidebarState();
    initSidebarToggle();
    initMenuAccordion();
});

function isDesktop() {
    return window.matchMedia(DESKTOP_MQ).matches;
}

function restoreSidebarState() {
    if (!isDesktop()) {
        return;
    }

    try {
        if (localStorage.getItem(STORAGE_KEY) === '1') {
            document.body.classList.add(SIDEBAR_COLLAPSED_CLASS);
        }
    } catch (err) {
        // Local storage unavailable, ignore
    }
}

function persistCollapsed(isCollapsed) {
    try {
        localStorage.setItem(STORAGE_KEY, isCollapsed ? '1' : '0');
    } catch (err) {
        // Ignore storage errors
    }
}

function initSidebarToggle() {
    document.querySelectorAll('[data-app-shell-toggle]').forEach((el) => {
        el.addEventListener('click', () => {
            if (isDesktop()) {
                const nextCollapsed = !document.body.classList.contains(SIDEBAR_COLLAPSED_CLASS);
                document.body.classList.toggle(SIDEBAR_COLLAPSED_CLASS, nextCollapsed);
                persistCollapsed(nextCollapsed);
            } else {
                document.body.classList.toggle(SIDEBAR_OPEN_CLASS);
            }
        });
    });

    document.querySelectorAll('[data-app-shell-close]').forEach((el) => {
        el.addEventListener('click', () => {
            document.body.classList.remove(SIDEBAR_OPEN_CLASS);
        });
    });

    // Sync state saat berpindah breakpoint
    const mediaQuery = window.matchMedia(DESKTOP_MQ);
    const onChange = (event) => {
        if (event.matches) {
            // Desktop: mobile overlay harus off
            document.body.classList.remove(SIDEBAR_OPEN_CLASS);
        } else {
            // Mobile: collapsed class tidak relevan (CSS media query sudah handle layout)
            document.body.classList.remove(SIDEBAR_COLLAPSED_CLASS);
        }
    };

    if (typeof mediaQuery.addEventListener === 'function') {
        mediaQuery.addEventListener('change', onChange);
    } else if (typeof mediaQuery.addListener === 'function') {
        mediaQuery.addListener(onChange);
    }
}

function initMenuAccordion() {
    document.querySelectorAll('[data-app-menu-toggle]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const item = button.closest('.app-menu-item');

            if (!item) {
                return;
            }

            const isExpanded = item.getAttribute('aria-expanded') === 'true';

            // Tutup sibling di level yang sama (satu grup terbuka per level)
            const parentList = item.parentElement;
            if (parentList) {
                parentList.querySelectorAll(':scope > .app-menu-item[aria-expanded="true"]').forEach((sibling) => {
                    if (sibling !== item) {
                        sibling.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            item.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
        });
    });
}
