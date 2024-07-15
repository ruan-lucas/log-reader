<?php

namespace App\Reports;

use App\Exports\AverageTimesByServiceExport;
use App\Reports\Contracts\ReportGeneratorInterface;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AverageTimesByServiceReport implements ReportGeneratorInterface
{
    public function generate(): BinaryFileResponse
    {
        return ExcelFacade::download(new AverageTimesByServiceExport, 'relatorio_tempos_medios_por_servico.csv', Excel::CSV, ['Content-Type' => 'application/csv']);
    }
}
