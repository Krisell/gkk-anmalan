<?php

use App\Http\Controllers\EndToEndTestingController;
use Illuminate\Support\Facades\Route;

Route::prefix('__e2e__')->controller(EndToEndTestingController::class)->group(function () {
    Route::post('/factory', 'factory');
    Route::post('/login', 'login');
    Route::post('/loginSuperadmin', 'loginSuperadmin');
    Route::post('/first', 'first');
    Route::post('/update', 'update');
    Route::post('/loadExam', 'loadExam');
    Route::post('/php', 'php');
});
