<?php

namespace App\Exports;

use App\Repositories\RequestRepositoryInterface;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AverageTimesByServiceExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Serviço',
            'Tempo médio de resposta',
            'Tempo médio de processamento de requisição',
            'Tempo médio de execução de plugins',
        ];
    }

    public function collection()
    {
        $requestRepository = app(RequestRepositoryInterface::class);

        return $requestRepository->getAverageTimesByService();
    }
}
