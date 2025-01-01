<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/old_home', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('home/index');
});

Route::get('/content', function () {
    return view('home/content/index');
});

Route::get('/contact', function () {
    return view('home/content/contact');
});

Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
