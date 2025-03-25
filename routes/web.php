<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/old_home', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('home/index');
});

Route::get('/about', function () {
    return view('home/content/index');
});

Route::get('/contact', function () {
    return view('home/content/contact');
});

Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
