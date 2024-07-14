<?php

use App\Http\Controllers\LogFileController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PanelController::class, 'index'])->name('welcome');
Route::get('/log-files', [LogFileController::class, 'index'])->name('logReader.files');
Route::post('/log-files', [LogFileController::class, 'upload'])->name('logReader.uploadLogFile');
Route::get('/generate-report', [ReportController::class, 'generateReport'])->name('report.generateReport');
