<?php

namespace App\Jobs;

use App\Models\Request as LogRequest;
use App\Repositories\Contracts\ConsumerRepositoryInterface;
use App\Repositories\Contracts\RequestRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessLogFile implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    protected $startLine;

    protected $endLine;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filePath, int $startLine, int $endLine)
    {
        $this->filePath = $filePath;
        $this->startLine = $startLine;
        $this->endLine = $endLine;
    }

    /**
     * Execute the job.
     */
    public function handle(ConsumerRepositoryInterface $consumerRepository, ServiceRepositoryInterface $serviceRepository, RequestRepositoryInterface $requestRepository): void
    {
        if (! Storage::exists($this->filePath)) {
            Log::error("O arquivo não existe no caminho especificado: {$this->filePath}");

            return;
        }

        // Lê o conteúdo do arquivo de log
        $rawLogs = Storage::get($this->filePath);

        // Separa cada log em uma linha
        $allLogs = explode("\n", trim($rawLogs));

        // Pega apenas as linhas que estão dentro do intervalo especificado
        $logs = array_slice($allLogs, $this->startLine, $this->endLine - $this->startLine);

        foreach ($logs as $log) {
            // Converte o log de JSON para array
            $data = json_decode($log, true);

            // Extrai os UUIDs do consumidor e do serviço
            $consumerUuid = $data['authenticated_entity']['consumer_id']['uuid'] ?? null;
            $serviceUuid = $data['service']['id'] ?? null;

            // Usa o método updateOrCreate para evitar condições de corrida
            if ($consumerUuid) {
                $consumer = $consumerRepository->updateOrCreate(
                    ['uuid' => $consumerUuid],
                    ['uuid' => $consumerUuid]
                );
            }

            if ($serviceUuid) {
                $service = $serviceRepository->updateOrCreate(
                    ['uuid' => $serviceUuid],
                    ['uuid' => $serviceUuid]
                );
            }

            // Cria um novo log de requisição
            $logRequest = new LogRequest();
            $logRequest->consumer_uuid = $consumer->uuid;
            $logRequest->service_uuid = $service->uuid;
            $logRequest->method = $data['request']['method'];
            $logRequest->uri = $data['request']['uri'];
            $logRequest->status = $data['response']['status'];
            $logRequest->request_time = $data['latencies']['request'];
            $logRequest->proxy_time = $data['latencies']['proxy'];
            $logRequest->gateway_time = $data['latencies']['gateway'];
            $logRequest->started_at = date('Y-m-d H:i:s', $data['started_at'] / 1000);

            $requestRepository->save($logRequest);
        }
    }
}
