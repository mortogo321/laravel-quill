<?php

use Illuminate\Support\Facades\Route;
use Mortogo321\LaravelQuill\Http\Controllers\UploadController;

Route::group([
    'prefix' => config('quill.routes.prefix', 'quill'),
    'middleware' => config('quill.routes.middleware', ['web']),
    'as' => 'quill.',
], function () {
    Route::post('/upload', [UploadController::class, 'upload'])->name('upload');
    Route::delete('/upload', [UploadController::class, 'delete'])->name('upload.delete');
});
