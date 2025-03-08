<?php

use App\Http\Controllers\TemplateController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::middleware(Authenticate::class)->group(function () {
    Route::get('admin/templates/{template}/edit', [TemplateController::class, 'pageEditor'])->name('page-editor');
});

Route::get('/', [TemplateController::class, 'home']);
Route::get('about', [TemplateController::class, 'about']);
Route::get('contact', [TemplateController::class, 'contact']);
