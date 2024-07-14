<?php

namespace App\Services;

use App\Jobs\ProcessLogFile;
use App\Repositories\LogFileProcessRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Throwable;

class LogFileService
{
    public function __construct(protected LogFileProcessRepositoryInterface $logFileProcessRepository)
    {
        $this->logFileProcessRepository = $logFileProcessRepository;
    }

    /**
     * Processa um arquivo de log, dividindo-o em partes e enviando para a fila
     */
    public function processLogFile(string $path): string
    {
        // Instância do arquivo de log
        $file = Storage::get($path);

        // Separa as linhas do arquivo de log
        $fileRows = explode("\n", $file);

        // Contagem de linhas do arquivo de log
        $lineCount = count($fileRows);

        // Definição do tamanho de cada parte do arquivo de log
        $chunkSize = 25000;

        // Calcular quantas partes o arquivo de log será dividido
        $chunks = intval(ceil($lineCount / $chunkSize));

        // Cria um array para armazenar os jobs
        $batchContent = [];

        for ($chunk = 1; $chunk <= $chunks; $chunk++) {
            // Calcular as linhas iniciais e finais de cada parte do arquivo de log
            $startLine = ($chunk - 1) * $chunkSize;
            $endLine = $chunk * $chunkSize;

            // Adiciono o job ao batch
            $batchContent[] = new ProcessLogFile($path, $startLine, $endLine);
        }

        $processJob = $this->logFileProcessRepository->create([
            'status' => 'processing',
        ]);

        // Crio o batch de jobs
        $batch = Bus::batch($batchContent)
            ->then(function ($batch) use ($processJob) {
                $this->logFileProcessRepository->update($processJob->id, ['status' => 'finished']);
            })
            ->catch(function ($batch, Throwable $e) use ($processJob) {
                $this->logFileProcessRepository->update($processJob->id, ['status' => 'failed', 'error_message' => $e->getMessage()]);
            })
            ->finally(function ($batch) use ($path) {
                // Deleta o arquivo de log ao final do processamento
                Storage::delete($path);
            })
            ->dispatch();

        // Atualiza o batch_id no registro do log_file_process
        $this->logFileProcessRepository->update($processJob->id, ['batch_id' => $batch->id]);

        return $batch->id;
    }

    public function getAllFilesProcessed(): Collection
    {
        return $this->logFileProcessRepository->all();
    }
}
