<?php

namespace App\Reports;

use App\Exports\RequestsByConsumerExport;
use App\Reports\Contracts\ReportGeneratorInterface;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RequestsByConsumerReport implements ReportGeneratorInterface
{
    public function generate(): BinaryFileResponse
    {
        return ExcelFacade::download(new RequestsByConsumerExport, 'relatorio_requisicoes_por_consumidor.csv', Excel::CSV, ['Content-Type' => 'application/csv']);
    }
}
