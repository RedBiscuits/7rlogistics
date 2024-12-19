<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('NewHomeContent');
});

Route::get('/content', function () {
    return view('NewHomeContent');
});
