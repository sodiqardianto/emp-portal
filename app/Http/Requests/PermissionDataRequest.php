<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionDataRequest extends FormRequest
{
    private const ALLOWED_SORT_FIELDS = ['permission_code', 'permission_name', 'sort_order', 'is_active'];

    private const ALLOWED_FILTER_FIELDS = ['permission_code', 'permission_name', 'description', 'sort_order', 'is_active'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function queryParams(): array
    {
        return [
            'search' => trim((string) $this->input('search', '')),
            'is_active' => $this->normalizeActiveFilter($this->input('is_active')),
            'filters' => $this->normalizeFilters($this->input('filters', [])),
            'sortBy' => in_array($this->input('sortBy'), self::ALLOWED_SORT_FIELDS, true)
                ? $this->input('sortBy')
                : 'sort_order',
            'sortDir' => $this->input('sortDir', 'asc'),
            'pageSize' => (int) $this->input('pageSize', 10),
            'page' => (int) $this->input('page', 1),
        ];
    }

    private function normalizeActiveFilter(?string $value): string
    {
        $value = strtoupper(trim((string) $value));

        return in_array($value, ['Y', 'N'], true) ? $value : '';
    }

    private function normalizeFilters(mixed $filters): array
    {
        if (! is_array($filters)) {
            return [];
        }

        $normalized = [];

        foreach ($filters as $filter) {
            if (! is_array($filter)) {
                continue;
            }

            $field = (string) ($filter['field'] ?? '');
            $value = trim((string) ($filter['value'] ?? ''));

            if ($value !== '' && in_array($field, self::ALLOWED_FILTER_FIELDS, true)) {
                $normalized[$field] = $value;
            }
        }

        return $normalized;
    }
}
