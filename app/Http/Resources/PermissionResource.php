<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'permission_code' => $this->permission_code,
            'permission_name' => $this->permission_name ?: $this->permission_code,
            'description' => $this->description,
            'sort_order' => $this->sort_order ? (int) $this->sort_order : null,
            'is_active' => $this->is_active,
            'create_date' => $this->create_date,
            'create_by' => $this->create_by,
            'update_date' => $this->update_date,
            'update_by' => $this->update_by,
            'menu_usage_count' => (int) ($this->menu_usage_count ?? 0),
            'user_usage_count' => (int) ($this->user_usage_count ?? 0),
        ];
    }
}
