<?php

namespace Tests\Unit;

use App\Exports\AverageTimesByServiceExport;
use App\Exports\RequestsByConsumerExport;
use App\Exports\RequestsByServiceExport;
use App\Repositories\LogFileProcessRepositoryInterface;
use App\Services\ReportService;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Mockery;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ReportServiceTest extends TestCase
{
    private $logFileProcessRepository;
    private $reportService;

    public function setUp(): void
    {
        parent::setUp();

        // Cria um mock para o repositório
        $this->logFileProcessRepository = Mockery::mock(LogFileProcessRepositoryInterface::class);

        // Instancia o ReportService com o mock do repositório
        $this->reportService = new ReportService($this->logFileProcessRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_generate_report_consumer_success()
    {
        // Configurar o mock para retornar uma coleção vazia (nenhum arquivo em processamento)
        $this->logFileProcessRepository->shouldReceive('findBy')
            ->with(['status' => 'processing'])
            ->andReturn(new EloquentCollection());
    
        // Simular a criação do arquivo CSV no armazenamento local
        Storage::fake('local');
        $filePath = 'relatorio_requisicoes_por_consumidor.csv';
        Storage::disk('local')->put($filePath, 'dummy content');
    
        // Configurar o mock do Excel para simular o download do arquivo
        ExcelFacade::shouldReceive('download')
            ->once()
            ->withArgs(function ($export, $fileName, $writerType, $headers) use ($filePath) {
                return $export instanceof RequestsByConsumerExport
                    && $fileName === $filePath
                    && $writerType === Excel::CSV
                    && $headers === ['Content-Type' => 'application/csv'];
            })
            ->andReturn(new BinaryFileResponse(Storage::disk('local')->path($filePath)));
    
        // Chamar o método generateReport com o tipo 'consumer'
        $response = $this->reportService->generateReport('consumer');
    
        // Verificar se a resposta é uma instância de BinaryFileResponse
        $this->assertInstanceOf(BinaryFileResponse::class, $response);
    }

    public function test_generate_report_service_success()
    {
        // Configurar o mock para retornar uma coleção vazia (nenhum arquivo em processamento)
        $this->logFileProcessRepository->shouldReceive('findBy')
            ->with(['status' => 'processing'])
            ->andReturn(new EloquentCollection());

        // Simular a criação do arquivo CSV no armazenamento local
        Storage::fake('local');
        $filePath = 'relatorio_requisicoes_por_servico.csv';
        Storage::disk('local')->put($filePath, 'dummy content');

        // Configurar o mock do Excel para simular o download do arquivo
        ExcelFacade::shouldReceive('download')
            ->once()
            ->withArgs(function ($export, $fileName, $writerType, $headers) use ($filePath) {
                return $export instanceof RequestsByServiceExport
                    && $fileName === $filePath
                    && $writerType === Excel::CSV
                    && $headers === ['Content-Type' => 'application/csv'];
            })
            ->andReturn(new BinaryFileResponse(Storage::disk('local')->path($filePath)));

        // Chamar o método generateReport com o tipo 'service'
        $response = $this->reportService->generateReport('service');

        // Verificar se a resposta é uma instância de BinaryFileResponse
        $this->assertInstanceOf(BinaryFileResponse::class, $response);
    }

    public function test_generate_report_average_times_success()
    {
        // Configurar o mock para retornar uma coleção vazia (nenhum arquivo em processamento)
        $this->logFileProcessRepository->shouldReceive('findBy')
            ->with(['status' => 'processing'])
            ->andReturn(new EloquentCollection());

        // Simular a criação do arquivo CSV no armazenamento local
        Storage::fake('local');
        $filePath = 'relatorio_tempos_medios_por_servico.csv';
        Storage::disk('local')->put($filePath, 'dummy content');

        // Configurar o mock do Excel para simular o download do arquivo
        ExcelFacade::shouldReceive('download')
            ->once()
            ->withArgs(function ($export, $fileName, $writerType, $headers) use ($filePath) {
                return $export instanceof AverageTimesByServiceExport
                    && $fileName === $filePath
                    && $writerType === Excel::CSV
                    && $headers === ['Content-Type' => 'application/csv'];
            })
            ->andReturn(new BinaryFileResponse(Storage::disk('local')->path($filePath)));

        // Chamar o método generateReport com o tipo 'average_times'
        $response = $this->reportService->generateReport('average_times');

        // Verificar se a resposta é uma instância de BinaryFileResponse
        $this->assertInstanceOf(BinaryFileResponse::class, $response);
    }

    public function test_generate_report_invalid_type()
    {
        // Configurar o mock para retornar uma coleção vazia (nenhum arquivo em processamento)
        $this->logFileProcessRepository->shouldReceive('findBy')
            ->with(['status' => 'processing'])
            ->andReturn(new EloquentCollection());

        // Chamar o método generateReport com um tipo inválido
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tipo de relatório inválido');
        $this->reportService->generateReport('invalid_type');
    }

    public function test_generate_report_file_processing()
    {
        // Configurar o mock para retornar uma coleção com um item (arquivo em processamento)
        $this->logFileProcessRepository->shouldReceive('findBy')
            ->with(['status' => 'processing'])
            ->andReturn(new EloquentCollection([['status' => 'processing']]));

        // Chamar o método generateReport
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Aguarde o processamento do arquivo de log');
        $this->reportService->generateReport('consumer');
    }

}
