<?php

use App\Packages\Controller\FileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/file-upload', [FileController::class, 'upload']);
});
