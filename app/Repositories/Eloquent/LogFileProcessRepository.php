<?php

namespace App\Repositories\Eloquent;

use App\Models\LogFileProcess;
use App\Repositories\Contracts\LogFileProcessRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LogFileProcessRepository implements LogFileProcessRepositoryInterface
{
    protected $model;

    public function __construct(LogFileProcess $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find($id): ?LogFileProcess
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

    public function firstBy(array $criteria): ?LogFileProcess
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }

    public function save(LogFileProcess $logFileProcess): void
    {
        $logFileProcess->save();
    }

    public function create(array $data): LogFileProcess
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): bool
    {
        $logFileProcess = $this->find($id);

        if ($logFileProcess) {
            return $logFileProcess->update($data);
        }

        return false;
    }

    public function updateOrCreate(array $criteria, array $data): LogFileProcess
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

    public function newInstance(): LogFileProcess
    {
        return new $this->model;
    }
}
