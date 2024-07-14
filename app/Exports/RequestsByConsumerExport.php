<?php

namespace App\Exports;

use App\Repositories\RequestRepositoryInterface;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RequestsByConsumerExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Consumidor',
            'Quantidade de requisições',
            'Tempo médio de resposta',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $requestRepository = app(RequestRepositoryInterface::class);

        return $requestRepository->getRequestsByConsumer();
    }
}
