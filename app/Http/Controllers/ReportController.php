<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateReportRequest;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    /**
     * Gera um relatório de acordo com o tipo informado.
     */
    public function generateReport(GenerateReportRequest $request)
    {
        $reportType = $request->input('reportType');

        return $this->reportService->generateReport($reportType);
    }
}
