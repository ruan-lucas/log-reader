<?php

namespace App\Repositories;

use App\Models\Consumer;
use Illuminate\Database\Eloquent\Collection;

interface ConsumerRepositoryInterface
{
    public function all(): Collection;

    public function find($id): ?Consumer;

    public function findBy(array $criteria): Collection;

    public function firstBy(array $criteria): ?Consumer;

    public function save(Consumer $service): void;

    public function create(array $data): Consumer;

    public function update($id, array $data): bool;

    public function updateOrCreate(array $criteria, array $data): Consumer;

    public function delete($id): void;

    public function paginate($perPage = 15);

    public function newInstance(): Consumer;
}
