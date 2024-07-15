<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadPostRequest;
use App\Services\LogFileService;

class LogFileController extends Controller
{
    public function __construct(protected LogFileService $logFileService) {}

    /**
     * Retorna a lista de arquivos de log processados
     */
    public function index()
    {
        $files = $this->logFileService->getAllFilesProcessed();

        return response()->json($files);
    }

    /**
     * Envia um arquivo de log para ser processado
     */
    public function upload(UploadPostRequest $request)
    {
        // Salva o arquivo de log no disco
        $path = $request->file('log_file')->store('log_files');

        // Processa o arquivo de log
        $this->logFileService->processLogFile($path);

        return redirect()->route('welcome');
    }
}
