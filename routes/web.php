<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('test', 'test')->name('test');
// Route::get('/login', function () {
//     return redirect(route('filament.admin.auth.login'));
// })->name('login');
