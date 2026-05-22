<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $connection = 'employee_sqlsrv';

    protected $table = 'BackOffice.dbo.tbl_menu';

    protected $primaryKey = 'id_menu';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    public function scopeActive(Builder $query): void
    {
        $query->whereRaw("ISNULL(is_active, '') = 'Y'");
    }

    public function scopeForBranch(Builder $query, string $branch): void
    {
        if ($branch === '') {
            return;
        }

        $query->where(function (Builder $q) use ($branch): void {
            $q->whereNull('Branch')
                ->orWhereRaw("LTRIM(RTRIM(Branch)) = ''")
                ->orWhere('Branch', $branch);
        });
    }
}
