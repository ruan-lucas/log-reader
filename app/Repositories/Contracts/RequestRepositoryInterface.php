<?php

namespace App\Repositories\Contracts;

use App\Models\Request;
use Illuminate\Database\Eloquent\Collection;

interface RequestRepositoryInterface
{
    public function all(): Collection;

    public function find($id): ?Request;

    public function findBy(array $criteria): Collection;

    public function firstBy(array $criteria): ?Request;

    public function save(Request $service): void;

    public function create(array $data): Request;

    public function update($id, array $data): bool;

    public function updateOrCreate(array $criteria, array $data): Request;

    public function delete($id): void;

    public function paginate($perPage = 15);

    public function newInstance(): Request;

    public function getRequestsByConsumer(): Collection;

    public function getRequestsByService(): Collection;

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Request>
     */
    public function getAverageTimesByService(): Collection;
}
