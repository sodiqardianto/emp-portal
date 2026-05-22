/**
 * Employee Access Table (List Page)
 * Self-contained AJAX table: fetch, filter, sort, pagination, edit link, delete
 */
(function () {
    const root = document.getElementById('app-table');
    if (!root) return;

    const canEdit = root.dataset.canEdit === '1';
    const canDelete = root.dataset.canDelete === '1';
    const dataUrl = root.dataset.url;
    const editUrlTpl = root.dataset.editUrl;
    const deleteUrlTpl = root.dataset.deleteUrl;

    const tbody = root.querySelector('tbody');
    const info = root.querySelector('[data-table-info]');
    const paginationEl = root.querySelector('[data-table-pagination]');
    const pageSizeSelect = root.querySelector('[data-table-pagesize]');
    const colCount = root.querySelector('thead tr').children.length;

    let state = { page: 1, pageSize: 10, sortBy: 'EmployeeCode', sortDir: 'asc', filters: {} };
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
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="app-table-empty"><i class="fa-regular fa-face-frown"></i> Data tidak ditemukan</td></tr>`;
        } else {
            tbody.innerHTML = data.map(renderRow).join('');
        }
        const from = (state.page - 1) * state.pageSize + 1;
        const to = Math.min(state.page * state.pageSize, total);
        info.textContent = total ? `${from}\u2013${to} dari ${total} data` : '0 data';
        renderPagination(lastPage);
    }

    // =========================================================================
    // ROW RENDERING
    // =========================================================================

    function renderRow(row) {
        const code = esc(row.EmployeeCode);
        const btns = [];

        if (canEdit) {
            const editUrl = editUrlTpl.replace('__CODE__', encodeURIComponent(row.EmployeeCode));
            btns.push(`<a href="${editUrl}" class="app-icon-btn app-icon-btn--primary" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>`);
        }
        if (canDelete) {
            btns.push(`<button type="button" class="app-icon-btn app-icon-btn--danger js-delete" data-code="${code}" data-name="${esc(row.EmployeeName)}" title="Delete"><i class="fa-solid fa-trash"></i></button>`);
        }

        const actions = btns.length
            ? `<div class="d-flex justify-content-end gap-2">${btns.join('')}</div>`
            : '<span class="text-muted">-</span>';

        return `<tr class="app-table-row">
            <td class="app-table-td app-table-td--code">${code}</td>
            <td class="app-table-td fw-semibold">${esc(row.EmployeeName)}</td>
            <td class="app-table-td">${esc(row.DivName || '')}</td>
            <td class="app-table-td">${esc(row.DeptName || '')}</td>
            <td class="app-table-td">${esc(row.UnitName || '')}</td>
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
        if (start > 1) { btns.push(pgBtn('1', 1)); if (start > 2) btns.push('<span class="app-table-pg-dots">\u2026</span>'); }
        for (let p = start; p <= end; p++) btns.push(pgBtn(String(p), p, false, p === state.page));
        if (end < lastPage) { if (end < lastPage - 1) btns.push('<span class="app-table-pg-dots">\u2026</span>'); btns.push(pgBtn(String(lastPage), lastPage)); }
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

    root.querySelectorAll('.app-table-th.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const field = th.dataset.field;
            if (state.sortBy === field) {
                if (state.sortDir === 'asc') state.sortDir = 'desc';
                else { state.sortBy = 'EmployeeCode'; state.sortDir = 'asc'; }
            } else { state.sortBy = field; state.sortDir = 'asc'; }
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

    root.querySelectorAll('.app-table-filter').forEach(input => {
        input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                state.filters[input.dataset.field] = input.value.trim();
                state.page = 1;
                fetchData();
            }, 400);
        });
    });

    pageSizeSelect.addEventListener('change', () => { state.pageSize = parseInt(pageSizeSelect.value); state.page = 1; fetchData(); });
    paginationEl.addEventListener('click', (e) => { const btn = e.target.closest('.app-table-pg-btn'); if (!btn || btn.disabled) return; state.page = parseInt(btn.dataset.page); fetchData(); });

    // Delete
    tbody.addEventListener('click', (e) => {
        const deleteBtn = e.target.closest('.js-delete');
        if (deleteBtn && canDelete) {
            const modalEl = document.getElementById('modal-delete');
            modalEl.querySelector('.delete-name').textContent = deleteBtn.dataset.name;
            modalEl.querySelector('.js-confirm-delete').dataset.code = deleteBtn.dataset.code;
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    });

    document.querySelector('.js-confirm-delete')?.addEventListener('click', (e) => {
        const form = document.getElementById('form-delete');
        form.action = deleteUrlTpl.replace('__CODE__', encodeURIComponent(e.currentTarget.dataset.code));
        form.submit();
    });

    function esc(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }

    updateSortUI();
    fetchData();
})();
