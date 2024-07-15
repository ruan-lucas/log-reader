<?php

namespace App\Repositories\Eloquent;

use App\Models\Consumer;
use App\Repositories\Contracts\ConsumerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ConsumerRepository implements ConsumerRepositoryInterface
{
    protected $model;

    public function __construct(Consumer $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find($id): ?Consumer
    {
        return $this->model->find($id);
    }

    public function firstBy(array $criteria): ?Consumer
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }

    public function findBy(array $criteria): Collection
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get();
    }

    public function save(Consumer $consumer): void
    {
        $consumer->save();
    }

    public function create(array $data): Consumer
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): bool
    {
        $consumer = $this->find($id);

        if ($consumer) {
            return $consumer->update($data);
        }

        return false;
    }

    public function updateOrCreate(array $criteria, array $data): Consumer
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

    public function newInstance(): Consumer
    {
        return new $this->model;
    }
}
