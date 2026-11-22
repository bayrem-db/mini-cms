<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/heure', function () {
    return view('heure', [
        'heure' => now()->format('H:i'),
        'date' => now()->format('d/m/Y'),
    ]);
});

Route::get('/a-propos', function () {
    return view('a-propos', [
        'auteur' => 'Bayrem',
        'groupe' => 'MDW32',
    ]);
});
