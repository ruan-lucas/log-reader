<?php

namespace App\Exports;

use App\Repositories\Contracts\RequestRepositoryInterface;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RequestsByServiceExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Serviço',
            'Quantidade de requisições',
            'Tempo médio de resposta',
        ];
    }

    public function collection()
    {
        $requestRepository = app(RequestRepositoryInterface::class);

        return $requestRepository->getRequestsByService();
    }
}
