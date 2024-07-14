<?php

namespace App\Http\Controllers;

use App\Services\LogFileService;
use Illuminate\Http\Request;

class LogFileController extends Controller
{
    public function __construct(protected LogFileService $logFileService)
    {
        $this->logFileService = $logFileService;
    }

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
    public function upload(Request $request)
    {
        // Validação para garantir que o arquivo de log foi enviado
        $request->validate([
            'log_file' => 'required|file|extensions:txt',
        ]);

        // Salva o arquivo de log no disco
        $path = $request->file('log_file')->store('log_files');

        // Processa o arquivo de log
        $this->logFileService->processLogFile($path);

        return redirect()->route('welcome');
    }
}
