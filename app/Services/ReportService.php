<?php

namespace App\Services;

use App\Exports\AverageTimesByServiceExport;
use App\Exports\RequestsByConsumerExport;
use App\Exports\RequestsByServiceExport;
use App\Repositories\LogFileProcessRepositoryInterface;
use InvalidArgumentException;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
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

        switch ($reportType) {
            case 'consumer':
                return ExcelFacade::download(new RequestsByConsumerExport, 'relatorio_requisicoes_por_consumidor.csv', Excel::CSV, ['Content-Type' => 'application/csv']);
            case 'service':
                return ExcelFacade::download(new RequestsByServiceExport, 'relatorio_requisicoes_por_servico.csv', Excel::CSV, ['Content-Type' => 'application/csv']);
            case 'average_times':
                return ExcelFacade::download(new AverageTimesByServiceExport, 'relatorio_tempos_medios_por_servico.csv', Excel::CSV, ['Content-Type' => 'application/csv']);
            default:
                throw new InvalidArgumentException('Tipo de relatório inválido');
        }
    }
}
