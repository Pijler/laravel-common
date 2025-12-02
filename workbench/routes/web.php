<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'impersonate'])->group(function () {
    Route::get('/', function () {
        return response()->json(['message' => 'OK']);
    });

    Route::get('/personal', function () {
        return response()->json(['message' => 'OK']);
    })->middleware('protect.impersonate');
});
