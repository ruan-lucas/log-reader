<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Gera um relatório de acordo com o tipo informado.
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'reportType' => 'required|string|in:consumer,service,average_times',
        ]);

        $reportType = $request->input('reportType');

        return $this->reportService->generateReport($reportType);
    }
}
