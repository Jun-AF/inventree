<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Routing login form
Route::get('login', [LoginController::class , 'showLoginForm']);
// Routing login and logut
Route::post('login', [LoginController::class , 'login'])
    ->name('login');
Route::post('logout', [LoginController::class , 'logout'])
    ->name('logout');
    
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
