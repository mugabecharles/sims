<?php

use Illuminate\Support\Facades\Route;

// API v1 routes (placeholder for future mobile/integration use)
Route::prefix('v1')->group(function () {
    Route::get('/ping', fn() => response()->json(['status' => 'ok', 'system' => 'SIMS']));
});
