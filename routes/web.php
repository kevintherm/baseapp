<?php

use App\Http\Controllers\TemplateController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::middleware(Authenticate::class)->group(function () {
    Route::post('admin/templates/{template}/edit', [TemplateController::class, 'savePageEditor'])->name('page-editor');
    Route::get('admin/templates/{template}/edit', [TemplateController::class, 'pageEditor'])->name('page-editor');
});

Route::redirect('/', 'home');
Route::get('/{path}', [TemplateController::class, 'routes'])->where('path', '.*');
