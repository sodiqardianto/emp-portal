<?php

namespace App\Repositories;

use App\Models\Employee;

class EmployeeRepository
{
    public function findActiveByCode(string $employeeCode): ?Employee
    {
        return Employee::query()
            ->select([
                'EmployeeCode',
                'EmployeeName',
                'HOCode',
                'HOName',
                'DivCode',
                'DivName',
                'DeptCode',
                'DeptName',
                'UnitCode',
                'UnitName',
                'PositionCode',
                'PositionName',
                'MailPrivate',
                'Password',
                'PasswordNew',
            ])
            ->where('EmployeeCode', $employeeCode)
            ->first();
    }

    public function updatePasswordNew(Employee $employee, string $hashedPassword): void
    {
        $employee->forceFill([
            'PasswordNew' => $hashedPassword,
        ])->save();
    }
}
