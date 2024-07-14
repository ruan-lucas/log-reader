<?php

namespace App\Repositories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

interface ServiceRepositoryInterface
{
    public function all(): Collection;

    public function find($id): ?Service;

    public function findBy(array $criteria): Collection;

    public function firstBy(array $criteria): ?Service;

    public function save(Service $service): void;

    public function create(array $data): Service;

    public function update($id, array $data): bool;

    public function updateOrCreate(array $criteria, array $data): Service;

    public function delete($id): void;

    public function paginate($perPage = 15);

    public function newInstance(): Service;
}
