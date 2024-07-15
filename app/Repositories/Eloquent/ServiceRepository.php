<?php

namespace App\Repositories\Eloquent;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository implements ServiceRepositoryInterface
{
    protected $model;

    public function __construct(Service $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find($id): ?Service
    {
        return $this->model->find($id);
    }

    public function findBy(array $criteria): Collection
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get();
    }

    public function firstBy(array $criteria): ?Service
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }

    public function save(Service $service): void
    {
        $service->save();
    }

    public function create(array $data): Service
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): bool
    {
        $service = $this->find($id);

        if ($service) {
            return $service->update($data);
        }

        return false;
    }

    public function updateOrCreate(array $criteria, array $data): Service
    {
        return $this->model->updateOrCreate($criteria, $data);
    }

    public function delete($id): void
    {
        $this->model->destroy($id);
    }

    public function paginate($perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    public function newInstance(): Service
    {
        return new $this->model;
    }
}
