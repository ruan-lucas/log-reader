<?php

namespace Tests\Unit;

use App\Models\LogFileProcess;
use App\Repositories\Contracts\LogFileProcessRepositoryInterface;
use App\Services\LogFileService;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class LogFileServiceTest extends TestCase
{
    private $logFileProcessRepository;

    private $logFileService;

    public function setUp(): void
    {
        parent::setUp();

        // Cria um mock para o repositório
        $this->logFileProcessRepository = Mockery::mock(LogFileProcessRepositoryInterface::class);

        // Instancia o LogFileService com o mock do repositório
        $this->logFileService = new LogFileService($this->logFileProcessRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_it_can_get_all_files_processed()
    {
        $this->logFileProcessRepository->shouldReceive('all')
            ->once()
            ->andReturn(new EloquentCollection([
                ['status' => 'processing'],
                ['status' => 'finished'],
                ['status' => 'failed'],
            ]));

        $files = $this->logFileService->getAllFilesProcessed();

        $this->assertInstanceOf(EloquentCollection::class, $files);

        $this->assertCount(3, $files);

    }

    public function testProcessLogFile()
    {
        // Caminho do arquivo de log fictício
        $filePath = 'log_files/test_log_file.log';

        // Conteúdo fictício do arquivo de log com 100 linhas
        $logContent = implode("\n", array_fill(0, 100, 'test log line'));

        // Cria um fake storage para simular o armazenamento de arquivos
        Storage::fake('local');
        Storage::put($filePath, $logContent);

        // Cria um processo de log fictício
        $processJob = new LogFileProcess(['status' => 'processing']);
        $processJob->id = 1; // Força o ID do processo pois no construct não é possível setar

        $this->logFileProcessRepository->shouldReceive('create')
            ->once()
            ->with(['status' => 'processing'])
            ->andReturn($processJob);

        // Configura o mock para os métodos de update
        $this->logFileProcessRepository->shouldReceive('update')
            ->with($processJob->id, Mockery::on(function ($arg) {
                return isset($arg['batch_id']) || isset($arg['status']);
            }))
            ->andReturnUsing(function ($id, $data) use ($processJob) {
                if (isset($data['batch_id'])) {
                    $processJob->batch_id = $data['batch_id'];
                }
                if (isset($data['status'])) {
                    $processJob->status = $data['status'];
                }

                return true;
            });

        // Configura o fake do Bus
        Bus::fake();

        // Chama o método a ser testado
        $batchId = $this->logFileService->processLogFile($filePath);

        // Certifica a quantidade de batches que foram processados
        Bus::assertBatchCount(1);

        // Verifica se o batch_id foi retornado
        $this->assertNotNull($batchId);
    }
}
