<?php

use App\Http\Controllers\TemplateController;
use App\Http\Middleware\SecureTemplateMiddleware;
use App\Livewire\PageEditor;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

Route::middleware([Authenticate::class, SecureTemplateMiddleware::class])->group(function () {
    Route::get('admin/templates/{template}/{page}/edit', [TemplateController::class, 'pageEditor'])
        ->name('page-editor');
    Route::post('admin/templates/{template}/{page}/edit', [TemplateController::class, 'savePageEditor'])
        ->name('page-editor');
    Route::post('/upload-image', [TemplateController::class, 'assetManager']);
});

Route::redirect('/', 'home');
Route::get('/{path}', [TemplateController::class, 'routes'])->where('path', '.*');
