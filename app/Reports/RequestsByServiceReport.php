<?php

namespace App\Reports;

use App\Exports\RequestsByServiceExport;
use App\Reports\Contracts\ReportGeneratorInterface;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RequestsByServiceReport implements ReportGeneratorInterface
{
    public function generate(): BinaryFileResponse
    {
        return ExcelFacade::download(new RequestsByServiceExport, 'relatorio_requisicoes_por_servico.csv', Excel::CSV, ['Content-Type' => 'application/csv']);
    }
}
