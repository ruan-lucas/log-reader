<?php

namespace App\Repositories\Eloquent;

use App\Models\Request;
use App\Repositories\RequestRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RequestRepository implements RequestRepositoryInterface
{
    protected $model;

    public function __construct(Request $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find($id): ?Request
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

    public function firstBy(array $criteria): ?Request
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }

    public function save(Request $request): void
    {
        $request->save();
    }

    public function create(array $data): Request
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): bool
    {
        $request = $this->find($id);

        if ($request) {
            return $request->update($data);
        }

        return false;
    }

    public function updateOrCreate(array $criteria, array $data): Request
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

    public function newInstance(): Request
    {
        return new $this->model;
    }

    public function getRequestsByConsumer(): Collection
    {
        return $this->model->selectRaw('consumer_uuid, count(*) as request_count, round(avg(request_time), 2) as avg_request_time')
            ->groupBy('consumer_uuid')
            ->orderByDesc('request_count')
            ->get();
    }

    public function getRequestsByService(): Collection
    {
        return $this->model->selectRaw('service_uuid, count(*) as request_count, round(avg(request_time), 2) as avg_request_time')
            ->groupBy('service_uuid')
            ->orderByDesc('request_count')
            ->get();
    }

    public function getAverageTimesByService(): Collection
    {
        return $this->model->selectRaw('
                service_uuid,
                round(avg(request_time), 2) as avg_request_time,
                round(avg(proxy_time), 2) as avg_proxy_time,
                round(avg(gateway_time), 2) as avg_gateway_time
            ')
            ->groupBy('service_uuid')
            ->orderBy('service_uuid')
            ->get();
    }
}
