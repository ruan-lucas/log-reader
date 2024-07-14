<?php

namespace Tests\Feature;

use App\Jobs\ProcessLogFile;
use App\Models\Request as LogRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessLogFileTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_processes_log_file_and_stores_requests()
    {
        Storage::fake('local');

        $filePath = 'test-logs/log-file.json';
        $logContent = '';

        // Cria 50 logs de exemplo com dados aleatórios
        for ($i = 0; $i < 50; $i++) {
            $logContent .= json_encode([
                'authenticated_entity' => ['consumer_id' => ['uuid' => fake()->uuid()]],
                'service' => ['id' => fake()->uuid()],
                'request' => ['method' => fake()->randomElement(['GET', 'POST', 'PUT', 'DELETE']), 'uri' => fake()->url()],
                'response' => ['status' => fake()->numberBetween(200, 500)],
                'latencies' => ['request' => fake()->numberBetween(50, 200), 'proxy' => fake()->numberBetween(25, 100), 'gateway' => fake()->numberBetween(25, 100)],
                'started_at' => fake()->unixTime() * 1000, // Convertendo para milissegundos
            ])."\n";
        }

        // Salva o conteúdo no arquivo de log
        Storage::put($filePath, $logContent);

        // Faço o dispatch do job ProcessLogFile
        ProcessLogFile::dispatch($filePath, 0, 100);

        // Verifica se foram criados 50 novos registros na tabela requests
        $this->assertDatabaseCount('requests', 50);

        // Verifica se os consumidores e serviços foram criados (ou reutilizados se já existirem)
        $uniqueConsumerUUIDs = LogRequest::distinct('consumer_uuid')->count();
        $uniqueServiceUUIDs = LogRequest::distinct('service_uuid')->count();

        $this->assertGreaterThanOrEqual(1, $uniqueConsumerUUIDs); // Pelo menos 1 consumidor
        $this->assertGreaterThanOrEqual(1, $uniqueServiceUUIDs); // Pelo menos 1 serviço

        // Remove o arquivo de log
        Storage::delete($filePath);
    }

    public function test_it_cant_process_nonexistent_log_file()
    {
        Storage::fake('local');

        $filePath = 'nonexistent-log-file.json';

        // Faço o dispatch do job ProcessLogFile
        ProcessLogFile::dispatch($filePath, 0, 100);

        // Verifica se o arquivo de log não existe
        $this->assertDatabaseCount('requests', 0);
    }

    public function test_it_logs_error_when_file_does_not_exist()
    {
        $filePath = 'nonexistent-log-file.json';

        // Faço o mock do log de erro
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) use ($filePath) {
                return $message === "O arquivo não existe no caminho especificado: {$filePath}";
            });

        // Faço o dispatch do job ProcessLogFile
        ProcessLogFile::dispatch($filePath, 0, 100);

        // Verifica se o arquivo de log não existe
        $this->assertDatabaseCount('requests', 0);

        // Certifica que o log de erro foi chamado
        Log::shouldHaveReceived('error');
    }
}
