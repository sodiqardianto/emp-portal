<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'employee_sqlsrv';

    protected $table = 'UM.dbo.MsEmployee';

    protected $primaryKey = 'EmployeeCode';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'PasswordNew',
    ];

    protected $hidden = [
        'Password',
        'PasswordNew',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('active', function (Builder $builder): void {
            $builder->where($builder->getModel()->qualifyColumn('IsActive'), 'Y');
        });
    }

    public function getAuthPassword(): ?string
    {
        return $this->PasswordNew;
    }

    public function getNameAttribute(): string
    {
        return (string) ($this->attributes['EmployeeName'] ?? '');
    }

    public function getEmailAttribute(): string
    {
        return (string) ($this->attributes['MailPrivate'] ?? '');
    }

    public function getMenuBranchAttribute(): string
    {
        $hoCode = trim((string) ($this->HOCode ?? ''));

        return $hoCode !== '' ? $hoCode : (string) config('services.employee_menu.branch', 'BHI');
    }
}
