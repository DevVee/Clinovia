<?php

namespace App\Repositories\Contracts;

use App\Models\Patient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PatientRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 20): LengthAwarePaginator;

    public function findById(int $id): ?Patient;

    public function create(array $data): Patient;

    public function update(Patient $patient, array $data): Patient;

    public function delete(Patient $patient): bool;
}
