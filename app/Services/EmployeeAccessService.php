<?php

namespace App\Services;

use App\Models\Employee;
use App\Repositories\EmployeeAccessRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeAccessService
{
    public function __construct(private readonly EmployeeAccessRepository $repository) {}

    public function paginate(array $criteria): LengthAwarePaginator
    {
        return $this->repository->paginate($criteria);
    }

    public function getEditData(string $employeeCode): array
    {
        $employee = $this->repository->findEmployee($employeeCode);

        if (! $employee) {
            throw (new ModelNotFoundException)->setModel(Employee::class, $employeeCode);
        }

        $matrix = $this->repository->getAccessMatrix($employeeCode);

        return [
            'employee' => $employee,
            ...$matrix,
        ];
    }

    public function save(string $employeeCode, array $assignments, string $actor): void
    {
        $employee = $this->repository->findEmployee($employeeCode);

        if (! $employee) {
            throw (new ModelNotFoundException)->setModel(Employee::class, $employeeCode);
        }

        $this->repository->replaceAccess($employeeCode, $assignments, $actor);
    }

    public function delete(string $employeeCode): void
    {
        $employee = $this->repository->findEmployee($employeeCode);

        if (! $employee) {
            throw (new ModelNotFoundException)->setModel(Employee::class, $employeeCode);
        }

        $this->repository->deleteAccess($employeeCode);
    }
}
