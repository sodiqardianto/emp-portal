<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuPermissionDataRequest extends FormRequest
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
        $sortBy = $this->input('sortBy');
        $allowedSort = ['nama_menu', 'urutan', 'is_active'];

        return [
            'search' => trim((string) $this->input('search', '')),
            'is_active' => in_array(strtoupper((string) $this->input('is_active', '')), ['Y', 'N'], true)
                ? strtoupper($this->input('is_active'))
                : '',
            'sortBy' => in_array($sortBy, $allowedSort, true) ? $sortBy : 'urutan',
            'sortDir' => $this->input('sortDir', 'asc') === 'desc' ? 'desc' : 'asc',
            'pageSize' => min(max((int) $this->input('pageSize', 10), 1), 100),
            'page' => max((int) $this->input('page', 1), 1),
        ];
    }
}
