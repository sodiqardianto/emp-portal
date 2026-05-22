/**
 * Permissions Table
 * Self-contained AJAX table: fetch, filter, sort, pagination, edit, delete
 */
(function () {
    const root = document.getElementById('app-table');
    if (!root) return;

    // --- Config ---
    const canEdit = root.dataset.canEdit === '1';
    const canDelete = root.dataset.canDelete === '1';
    const dataUrl = root.dataset.url;
    const updateUrlTpl = root.dataset.updateUrl;
    const deleteUrlTpl = root.dataset.deleteUrl;

    // --- DOM ---
    const tbody = root.querySelector('tbody');
    const info = root.querySelector('[data-table-info]');
    const paginationEl = root.querySelector('[data-table-pagination]');
    const pageSizeSelect = root.querySelector('[data-table-pagesize]');
    const colCount = root.querySelector('thead tr').children.length;

    // --- State ---
    let state = { page: 1, pageSize: 10, sortBy: 'sort_order', sortDir: 'asc', filters: {} };
    let rows = [];
    let debounceTimer = null;

    // =========================================================================
    // FETCH & RENDER
    // =========================================================================

    async function fetchData() {
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="app-table-loading"><i class="fa-solid fa-spinner fa-spin"></i> Memuat data...</td></tr>`;

        const params = new URLSearchParams();
        params.set('page', state.page);
        params.set('pageSize', state.pageSize);
        params.set('sortBy', state.sortBy);
        params.set('sortDir', state.sortDir);

        let i = 0;
        for (const [field, value] of Object.entries(state.filters)) {
            if (value) {
                params.set(`filters[${i}][field]`, field);
                params.set(`filters[${i}][value]`, value);
                i++;
            }
        }

        try {
            const res = await fetch(`${dataUrl}?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error(res.status);
            const json = await res.json();
            rows = json.data || [];
            render(rows, json.last_page || 1, json.last_row || 0);
        } catch {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="app-table-empty"><i class="fa-regular fa-face-frown"></i> Gagal memuat data</td></tr>`;
        }
    }

    function render(data, lastPage, total) {
        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="app-table-empty"><i class="fa-regular fa-face-frown"></i> Data permission tidak ditemukan</td></tr>`;
        } else {
            tbody.innerHTML = data.map(renderRow).join('');
        }

        const from = (state.page - 1) * state.pageSize + 1;
        const to = Math.min(state.page * state.pageSize, total);
        info.textContent = total ? `${from}\u2013${to} dari ${total} data` : '0 data';
        renderPagination(lastPage);
    }

    // =========================================================================
    // ROW RENDERING (sesuaikan kolom di sini untuk modul lain)
    // =========================================================================

    function renderRow(row) {
        const code = esc(row.permission_code);
        const isActive = row.is_active === 'Y';
        const hasUsage = (row.menu_usage_count || 0) > 0 || (row.user_usage_count || 0) > 0;

        // Action buttons
        const btns = [];
        if (canEdit) {
            btns.push(`<button type="button" class="app-icon-btn app-icon-btn--primary js-edit" data-code="${code}" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>`);
        }
        if (canDelete && !hasUsage) {
            btns.push(`<button type="button" class="app-icon-btn app-icon-btn--danger js-delete" data-code="${code}" title="Delete"><i class="fa-solid fa-trash"></i></button>`);
        } else if (canDelete && hasUsage) {
            btns.push(`<button type="button" class="app-icon-btn app-icon-btn--danger" disabled title="Permission masih dipakai"><i class="fa-solid fa-trash"></i></button>`);
        }
        const actions = btns.length
            ? `<div class="d-flex justify-content-end gap-2">${btns.join('')}</div>`
            : '<span class="text-muted">-</span>';

        return `<tr class="app-table-row">
            <td class="app-table-td app-table-td--code">${code}</td>
            <td class="app-table-td">${esc(row.permission_name)}</td>
            <td class="app-table-td app-table-td--desc">${esc(row.description || '')}</td>
            <td class="app-table-td text-center"><span class="app-badge ${isActive ? 'app-badge--success' : 'app-badge--danger'}">${isActive ? 'Aktif' : 'Nonaktif'}</span></td>
            <td class="app-table-td text-end">${actions}</td>
        </tr>`;
    }

    // =========================================================================
    // PAGINATION
    // =========================================================================

    function renderPagination(lastPage) {
        if (lastPage <= 1) { paginationEl.innerHTML = ''; return; }

        const btns = [];
        btns.push(pgBtn('<i class="fa-solid fa-angles-left"></i>', 1, state.page <= 1));
        btns.push(pgBtn('<i class="fa-solid fa-angle-left"></i>', state.page - 1, state.page <= 1));

        const start = Math.max(1, state.page - 2);
        const end = Math.min(lastPage, state.page + 2);

        if (start > 1) {
            btns.push(pgBtn('1', 1));
            if (start > 2) btns.push('<span class="app-table-pg-dots">\u2026</span>');
        }
        for (let p = start; p <= end; p++) {
            btns.push(pgBtn(String(p), p, false, p === state.page));
        }
        if (end < lastPage) {
            if (end < lastPage - 1) btns.push('<span class="app-table-pg-dots">\u2026</span>');
            btns.push(pgBtn(String(lastPage), lastPage));
        }

        btns.push(pgBtn('<i class="fa-solid fa-angle-right"></i>', state.page + 1, state.page >= lastPage));
        btns.push(pgBtn('<i class="fa-solid fa-angles-right"></i>', lastPage, state.page >= lastPage));
        paginationEl.innerHTML = btns.join('');
    }

    function pgBtn(label, page, disabled = false, active = false) {
        const cls = `app-table-pg-btn${active ? ' active' : ''}${disabled ? ' disabled' : ''}`;
        return `<button type="button" class="${cls}" data-page="${page}" ${disabled ? 'disabled' : ''}>${label}</button>`;
    }

    // =========================================================================
    // EVENTS
    // =========================================================================

    // Sort (3 states: asc → desc → default)
    root.querySelectorAll('.app-table-th.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const field = th.dataset.field;
            if (state.sortBy === field) {
                if (state.sortDir === 'asc') {
                    state.sortDir = 'desc';
                } else {
                    // Reset to default sort
                    state.sortBy = 'sort_order';
                    state.sortDir = 'asc';
                }
            } else {
                state.sortBy = field;
                state.sortDir = 'asc';
            }
            state.page = 1;
            updateSortUI();
            fetchData();
        });
    });

    function updateSortUI() {
        root.querySelectorAll('.app-table-th.sortable').forEach(th => {
            th.classList.remove('sort-asc', 'sort-desc');
            const icon = th.querySelector('.sort-icon');
            if (!icon) return;
            icon.className = 'fa-solid fa-sort sort-icon';
            if (th.dataset.field === state.sortBy) {
                th.classList.add(state.sortDir === 'asc' ? 'sort-asc' : 'sort-desc');
                icon.className = state.sortDir === 'asc' ? 'fa-solid fa-sort-up sort-icon' : 'fa-solid fa-sort-down sort-icon';
            }
        });
    }

    // Filters
    root.querySelectorAll('.app-table-filter').forEach(input => {
        const event = input.tagName === 'SELECT' ? 'change' : 'input';
        input.addEventListener(event, () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                state.filters[input.dataset.field] = input.value.trim();
                state.page = 1;
                fetchData();
            }, input.tagName === 'SELECT' ? 0 : 400);
        });
    });

    // Page size
    pageSizeSelect.addEventListener('change', () => {
        state.pageSize = parseInt(pageSizeSelect.value);
        state.page = 1;
        fetchData();
    });

    // Pagination
    paginationEl.addEventListener('click', (e) => {
        const btn = e.target.closest('.app-table-pg-btn');
        if (!btn || btn.disabled) return;
        state.page = parseInt(btn.dataset.page);
        fetchData();
    });

    // Row actions
    tbody.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.js-edit');
        const deleteBtn = e.target.closest('.js-delete');

        if (editBtn && canEdit) {
            const row = rows.find(r => r.permission_code === editBtn.dataset.code);
            if (row) openEditModal(row);
        }

        if (deleteBtn && canDelete) {
            openDeleteModal(deleteBtn.dataset.code);
        }
    });

    // =========================================================================
    // MODALS (sesuaikan form fields di sini untuk modul lain)
    // =========================================================================

    function openEditModal(row) {
        const form = document.getElementById('form-edit');
        const modalEl = document.getElementById('modal-edit');
        form.action = updateUrlTpl.replace('__CODE__', encodeURIComponent(row.permission_code));
        form.elements.permission_code.value = row.permission_code || '';
        form.elements.permission_name.value = row.permission_name || '';
        form.elements.description.value = row.description || '';
        form.elements.sort_order.value = row.sort_order || '';
        form.elements.is_active.value = row.is_active === 'N' ? 'N' : 'Y';
        window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    function openDeleteModal(code) {
        const modalEl = document.getElementById('modal-delete');
        modalEl.querySelector('.delete-code').textContent = code;
        modalEl.querySelector('.js-confirm-delete').dataset.code = code;
        window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    document.querySelector('.js-confirm-delete')?.addEventListener('click', (e) => {
        const code = e.currentTarget.dataset.code;
        const form = document.getElementById('form-delete');
        form.action = deleteUrlTpl.replace('__CODE__', encodeURIComponent(code));
        form.submit();
    });

    // =========================================================================
    // HELPERS
    // =========================================================================

    function esc(str) {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    }

    // --- Init ---
    updateSortUI();
    fetchData();
})();
