<?php

use Illuminate\Support\Facades\Route;
use AlessandroCampos224\QuantumPageBuilder\Http\Controllers\PageBuilderController;

Route::prefix('api/page-builder')->group(function () {
    Route::post('save', [PageBuilderController::class, 'save'])->name('page-builder.save');
    Route::get('load', [PageBuilderController::class, 'load'])->name('page-builder.load');
    Route::post('render', [PageBuilderController::class, 'render'])->name('page-builder.render');
}); 