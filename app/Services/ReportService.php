<?php

namespace App\Services;

use App\Reports\Factories\ReportGeneratorFactory;
use App\Repositories\Contracts\LogFileProcessRepositoryInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportService
{
    public function __construct(protected LogFileProcessRepositoryInterface $logFileProcessRepository)
    {
        $this->logFileProcessRepository = $logFileProcessRepository;
    }

    public function generateReport(string $reportType): BinaryFileResponse
    {
        $fileProcessing = $this->logFileProcessRepository->findBy(['status' => 'processing']);

        if ($fileProcessing->isNotEmpty()) {
            throw new InvalidArgumentException('Aguarde o processamento do arquivo de log');
        }

        $reportGenerator = ReportGeneratorFactory::make($reportType);

        return $reportGenerator->generate();
    }
}
