<?php

namespace App\Services;

use App\Reports\Factories\ReportGeneratorFactory;
use App\Repositories\Contracts\LogFileProcessRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportService
{
    public function __construct(protected LogFileProcessRepositoryInterface $logFileProcessRepository) {}

    public function generateReport(string $reportType): BinaryFileResponse|JsonResponse
    {
        $fileProcessing = $this->logFileProcessRepository->findBy(['status' => 'processing']);

        if ($fileProcessing->isNotEmpty()) {
            return response()->json(['message' => 'Aguarde o processamento do arquivo de log'], 400);
        }

        $reportGenerator = ReportGeneratorFactory::make($reportType);

        return $reportGenerator->generate();
    }
}
