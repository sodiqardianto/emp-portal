<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeMenuPermission extends Model
{
    protected $connection = 'employee_sqlsrv';

    protected $table = 'BackOffice.dbo.tbl_employee_menu_permission';

    public $incrementing = false;

    public $timestamps = false;
}
