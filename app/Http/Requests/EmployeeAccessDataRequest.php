<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeAccessDataRequest extends FormRequest
{
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
        $allowedSort = ['EmployeeCode', 'EmployeeName', 'DivName', 'DeptName', 'UnitName'];
        $sortBy = $this->input('sortBy');

        return [
            'filters' => $this->normalizeFilters($this->input('filters', [])),
            'sortBy' => in_array($sortBy, $allowedSort, true) ? $sortBy : 'EmployeeCode',
            'sortDir' => $this->input('sortDir', 'asc') === 'desc' ? 'desc' : 'asc',
            'pageSize' => min(max((int) $this->input('pageSize', 10), 1), 100),
            'page' => max((int) $this->input('page', 1), 1),
        ];
    }

    private function normalizeFilters(mixed $filters): array
    {
        if (! is_array($filters)) {
            return [];
        }

        $allowed = ['EmployeeCode', 'EmployeeName', 'DivName', 'DeptName', 'UnitName'];
        $normalized = [];

        foreach ($filters as $filter) {
            if (! is_array($filter)) continue;
            $field = $filter['field'] ?? '';
            $value = trim($filter['value'] ?? '');
            if ($value !== '' && in_array($field, $allowed, true)) {
                $normalized[] = ['field' => $field, 'value' => $value];
            }
        }

        return $normalized;
    }
}
