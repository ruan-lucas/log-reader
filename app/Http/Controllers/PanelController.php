<?php

namespace App\Http\Controllers;

use App\Services\LogFileService;

class PanelController extends Controller
{
    public function __construct(protected LogFileService $logFileService) {}

    /**
     * Retorna a view principal do painel.
     */
    public function index()
    {
        $logFiles = $this->logFileService->getAllFilesProcessed();

        return view('index', compact('logFiles'));
    }
}
