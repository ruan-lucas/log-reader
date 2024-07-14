<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LogFileControllerTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function test_it_can_upload_a_log_file_and_test_it_started_processing(): void
    {
        // Crio um fake storage para simular o armazenamento de arquivos
        Storage::fake('local');

        // Crio um fake bus para simular o processamento de jobs
        Bus::fake();

        // Crio um arquivo fake de log com 500 KB
        $file = UploadedFile::fake()->createWithContent('logfile.txt', str_repeat('a', 500 * 1024)); // 500 KB

        // Envio o arquivo de log para a rota de upload
        $response = $this->post('/log-files', [
            'log_file' => $file,
        ]);

        // Assert que o arquivo foi armazenado
        $response->assertStatus(302);

        // Garantir que não houve erros na sessão
        $response->assertSessionHasNoErrors();

        // Assert que o arquivo foi armazenado no disco
        Storage::disk('local')->assertExists('log_files/'.$file->hashName());

        // Certifica a quantidade de batches que foram processados
        Bus::assertBatchCount(1);

        // Cleanup fake storage
        Storage::disk('local')->delete('logs/'.$file->hashName());
    }

    public function test_it_requires_a_log_file_to_be_uploaded(): void
    {
        // Crio um fake storage para simular o armazenamento de arquivos
        Storage::fake('local');

        // Envio a requisição sem o arquivo de log
        $response = $this->post('/log-files', [], ['Accept' => 'application/json']);

        // Asseguro que a resposta foi um erro de validação
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('log_file');
    }

    public function test_it_validates_the_file_type(): void
    {
        // Crio um fake storage para simular o armazenamento de arquivos
        Storage::fake('local');

        // Crio um arquivo fake com tipo inválido
        $file = UploadedFile::fake()->create('logfile.pdf', 500); // 500 KB

        // Envio o arquivo de tipo inválido para a rota de upload
        $response = $this->post('/log-files', [
            'log_file' => $file,
        ]);

        $response->assertSessionHasErrors('log_file');
        $response->assertStatus(302);
    }

    public function test_it_returns_files_processed(): void
    {
        Bus::fake();

        // Crio um fake storage para simular o armazenamento de arquivos
        Storage::fake('local');

        // Crio um arquivo fake de log com 500 KB
        $file = UploadedFile::fake()->createWithContent('logfile.txt', str_repeat('a', 500 * 1024)); // 500 KB

        // Envio o arquivo de log para a rota de upload
        $this->post('/log-files', [
            'log_file' => $file,
        ], ['Accept' => 'application/json']);

        // Segundo envio de arquivo de log para a rota de upload
        $this->post('/log-files', [
            'log_file' => $file,
        ], ['Accept' => 'application/json']);

        $response = $this->get('/log-files', ['Accept' => 'application/json']);

        $response->assertJson([]);

        // Cleanup fake storage
        Storage::disk('local')->delete('logs/'.$file->hashName());
    }
}
