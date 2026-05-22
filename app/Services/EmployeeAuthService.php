<?php

namespace App\Services;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class EmployeeAuthService
{
    public function __construct(private readonly EmployeeRepository $employees) {}

    public function authenticate(string $username, string $password): ?Employee
    {
        $username = trim($username);

        if ($username === '' || $password === '') {
            return null;
        }

        $employee = $this->employees->findActiveByCode($username);

        if (! $employee || ! $this->passwordIsValid($employee, $password)) {
            return null;
        }

        return $employee;
    }

    private function passwordIsValid(Employee $employee, string $password): bool
    {
        if (! empty($employee->PasswordNew)) {
            try {
                if (Hash::check($password, (string) $employee->PasswordNew)) {
                    return true;
                }
            } catch (RuntimeException) {
                // Legacy rows may contain non-bcrypt value in PasswordNew.
            }
        }

        if (! hash_equals((string) $employee->Password, $password)) {
            return false;
        }

        $this->employees->updatePasswordNew(
            $employee,
            Hash::make((string) $employee->Password),
        );

        return true;
    }
}
