<?php

use Illuminate\Support\Facades\Route;

Route::get('/docs', function () {
    return view('swagger-ui');
})->name('api.docs');

Route::get('/api-docs', function () {
    return response()->file(storage_path('api-docs/openapi.yaml'));
})->name('api.docs.yaml');
