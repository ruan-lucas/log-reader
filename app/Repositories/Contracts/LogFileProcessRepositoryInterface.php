<?php

namespace App\Repositories\Contracts;

use App\Models\LogFileProcess;
use Illuminate\Database\Eloquent\Collection;

interface LogFileProcessRepositoryInterface
{
    public function all(): Collection;

    public function find($id): ?LogFileProcess;

    public function findBy(array $criteria): Collection;

    public function firstBy(array $criteria): ?LogFileProcess;

    public function save(LogFileProcess $service): void;

    public function create(array $data): LogFileProcess;

    public function update($id, array $data): bool;

    public function updateOrCreate(array $criteria, array $data): LogFileProcess;

    public function delete($id): void;

    public function paginate($perPage = 15);

    public function newInstance(): LogFileProcess;
}
