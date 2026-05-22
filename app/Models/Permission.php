<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $connection = 'employee_sqlsrv';

    protected $table = 'BackOffice.dbo.mst_permission';

    protected $primaryKey = 'permission_code';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'permission_code',
        'permission_name',
        'description',
        'sort_order',
        'is_active',
        'create_date',
        'create_by',
        'update_date',
        'update_by',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
