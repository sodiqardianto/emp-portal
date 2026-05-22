import { TabulatorFull as Tabulator } from 'tabulator-tables';
import 'tabulator-tables/dist/css/tabulator_bootstrap5.min.css';

const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

const replaceCode = (template, code) => template.replace('__CODE__', encodeURIComponent(code));

const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

const buildQueryUrl = (url, params) => {
    const query = new URLSearchParams();
    const sorter = Array.isArray(params.sorters) ? params.sorters[0] : null;
    const filters = Array.isArray(params.filters) ? params.filters : [];

    query.set('page', params.page ?? 1);
    query.set('pageSize', params.size ?? 10);

    if (sorter?.field) {
        query.set('sortBy', sorter.field);
        query.set('sortDir', sorter.dir === 'desc' ? 'desc' : 'asc');
    }

    filters.forEach((filter, index) => {
        if (! filter.field || filter.value === undefined || filter.value === null || filter.value === '') {
            return;
        }

        query.set(`filters[${index}][field]`, filter.field);
        query.set(`filters[${index}][type]`, filter.type || 'like');
        query.set(`filters[${index}][value]`, filter.value);
    });

    return `${url}?${query.toString()}`;
};

const statusFormatter = (cell) => {
    const value = cell.getValue();
    const isActive = value === 'Y';

    return `<span class="app-badge ${isActive ? 'app-badge--success' : 'app-badge--danger'}">${isActive ? 'Aktif' : 'Nonaktif'}</span>`;
};

const actionsFormatter = (cell, formatterParams) => {
    const row = cell.getData();
    const hasUsage = Number(row.menu_usage_count ?? 0) > 0 || Number(row.user_usage_count ?? 0) > 0;
    const code = escapeHtml(row.permission_code);
    const actions = [];

    if (formatterParams.canEdit) {
        actions.push(`
            <button type="button" class="app-icon-btn app-icon-btn--primary js-permission-edit" data-code="${code}" title="Edit">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        `);
    }

    if (formatterParams.canDelete) {
        actions.push(`
            <button type="button" class="app-icon-btn app-icon-btn--danger js-permission-delete" data-code="${code}" ${hasUsage ? 'disabled' : ''} title="${hasUsage ? 'Permission masih dipakai' : 'Delete'}">
                <i class="fa-solid fa-trash"></i>
            </button>
        `);
    }

    return actions.length
        ? `<div class="d-flex justify-content-end gap-2">${actions.join('')}</div>`
        : '<span class="text-muted">-</span>';
};

const fillEditForm = (form, row, actionUrl) => {
    form.action = actionUrl;
    form.elements.permission_code.value = row.permission_code ?? '';
    form.elements.permission_name.value = row.permission_name ?? '';
    form.elements.description.value = row.description ?? '';
    form.elements.sort_order.value = row.sort_order ?? '';
    form.elements.is_active.value = row.is_active === 'N' ? 'N' : 'Y';
};

document.addEventListener('DOMContentLoaded', () => {
    const tableElement = document.getElementById('permissions-table');

    if (! tableElement) {
        return;
    }

    const canEdit = tableElement.dataset.canEdit === '1';
    const canDelete = tableElement.dataset.canDelete === '1';
    const VISIBLE_SORT_FIELDS = ['permission_code', 'permission_name', 'is_active'];
    const requestedSortBy = tableElement.dataset.initialSortBy || '';
    const initialSortBy = VISIBLE_SORT_FIELDS.includes(requestedSortBy) ? requestedSortBy : 'permission_code';
    const initialSortDir = tableElement.dataset.initialSortDir === 'desc' ? 'desc' : 'asc';
    const pageSize = Number(tableElement.dataset.pageSize || 10);

    const table = new Tabulator(tableElement, {
        ajaxURL: tableElement.dataset.url,
        ajaxConfig: {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
        ajaxURLGenerator: (url, config, params) => buildQueryUrl(url, params),
        pagination: true,
        paginationMode: 'remote',
        paginationSize: pageSize,
        paginationSizeSelector: [10, 25, 50, 100],
        paginationCounter: 'rows',
        filterMode: 'remote',
        headerFilterLiveFilterDelay: 400,
        sortMode: 'remote',
        initialSort: [
            { column: initialSortBy, dir: initialSortDir },
        ],
        layout: 'fitDataTable',
        responsiveLayout: false,
        placeholder: 'Data permission tidak ditemukan.',
        columns: [
            { title: 'Code', field: 'permission_code', width: 150, headerSort: true, formatter: 'plaintext', headerFilter: 'input', headerFilterPlaceholder: 'Search' },
            { title: 'Name', field: 'permission_name', width: 220, headerSort: true, formatter: 'plaintext', headerFilter: 'input', headerFilterPlaceholder: 'Search' },
            { title: 'Description', field: 'description', width: 340, headerSort: false, formatter: 'textarea', headerFilter: 'input', headerFilterPlaceholder: 'Search' },
            { title: 'Status', field: 'is_active', width: 120, hozAlign: 'center', headerHozAlign: 'center', headerSort: true, formatter: statusFormatter, headerFilter: 'list', headerFilterParams: { values: { '': 'All', Y: 'Aktif', N: 'Nonaktif' }, clearable: true } },
            { title: 'Actions', field: 'permission_code', width: 130, hozAlign: 'right', headerHozAlign: 'right', headerSort: false, formatter: actionsFormatter, formatterParams: { canEdit, canDelete } },
        ],
    });

    tableElement.addEventListener('click', (event) => {
        if (! (event.target instanceof Element)) {
            return;
        }

        const editButton = event.target.closest('.js-permission-edit');
        const deleteButton = event.target.closest('.js-permission-delete');

        if (editButton && canEdit) {
            const row = table
                .getRows()
                .find((tableRow) => tableRow.getData().permission_code === editButton.dataset.code)
                ?.getData();
            const form = document.getElementById('kt_modal_edit_permission_form');
            const modalElement = document.getElementById('kt_modal_edit_permission');

            if (row && form && modalElement) {
                fillEditForm(form, row, replaceCode(tableElement.dataset.updateUrlTemplate, row.permission_code));
                window.bootstrap?.Modal.getOrCreateInstance(modalElement).show();
            }
        }

        if (deleteButton && canDelete && ! deleteButton.disabled) {
            const code = deleteButton.dataset.code;

            if (window.confirm(`Hapus permission ${code}?`)) {
                const form = document.getElementById('permissions-delete-form');

                if (form) {
                    form.action = replaceCode(tableElement.dataset.deleteUrlTemplate, code);
                    form.submit();
                }
            }
        }
    });
});
