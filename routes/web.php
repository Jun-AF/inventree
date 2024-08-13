<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HarvestingController;
use App\Http\Controllers\HaulingController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\RktController;
use App\Http\Controllers\ScalerController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Routing login form
Route::get('/login', [LoginController::class , 'showLoginForm']);
// Routing login and logut
Route::post('/login', [LoginController::class , 'login'])
    ->name('login');
Route::post('/logout', [LoginController::class , 'logout'])
    ->name('logout');
    
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/harvesting', [HarvestingController::class, 'index'])->name('harvesting');
Route::post('/harvesting/store', [HarvestingController::class, 'store'])->name('harvesting.store');
Route::patch('/harvesting/{key}/update', [HarvestingController::class, 'update'])->name('harvesting.update');
Route::delete('/harvesting/{key/delete}', [HarvestingController::class, 'destroy'])->name('harvesting.destroy');
Route::delete('/harvesting/truncate', [HarvestingController::class, 'truncate'])->name('harvesting.truncate');

Route::get('/hauling', [HaulingController::class, 'index'])->name('hauling');
Route::post('/hauling/store', [HaulingController::class, 'store'])->name('hauling.store');
Route::patch('/hauling/{key}/update', [HaulingController::class, 'update'])->name('hauling.update');
Route::delete('/hauling/{key/delete}', [HaulingController::class, 'destroy'])->name('hauling.destroy');
Route::delete('/hauling/truncate', [HaulingController::class, 'truncate'])->name('hauling.truncate');

Route::get('/measurement', [MeasurementController::class, 'index'])->name('measurement');
Route::post('/measurement/store', [MeasurementController::class, 'store'])->name('measurement.store');
Route::patch('/measurement/{key}/update', [MeasurementController::class, 'update'])->name('measurement.update');
Route::delete('/measurement/{key/delete}', [MeasurementController::class, 'destroy'])->name('measurement.destroy');
Route::delete('/measurement/truncate', [MeasurementController::class, 'truncate'])->name('measurement.truncate');

Route::get('/operator', [OperatorController::class, 'index'])->name('operator');
Route::post('/operator/store', [OperatorController::class, 'store'])->name('operator.store');
Route::patch('/operator/{key}/update', [OperatorController::class, 'update'])->name('operator.update');
Route::delete('/operator/{key/delete}', [OperatorController::class, 'destroy'])->name('operator.destroy');
Route::delete('/operator/truncate', [OperatorController::class, 'truncate'])->name('operator.truncate');

Route::get('/partner', [PartnerController::class, 'index'])->name('partner');
Route::post('/partner/store', [PartnerController::class, 'store'])->name('partner.store');
Route::patch('/partner/{key}/update', [PartnerController::class, 'update'])->name('partner.update');
Route::delete('/partner/{key/delete}', [PartnerController::class, 'destroy'])->name('partner.destroy');
Route::delete('/partner/truncate', [PartnerController::class, 'truncate'])->name('partner.truncate');

Route::get('/rkt', [RktController::class, 'index'])->name('rkt');
Route::post('/rkt/store', [RktController::class, 'store'])->name('rkt.store');
Route::patch('/rkt/{key}/update', [RktController::class, 'update'])->name('rkt.update');
Route::delete('/rkt/{key/delete}', [RktController::class, 'destroy'])->name('rkt.destroy');
Route::delete('/rkt/truncate', [RktController::class, 'truncate'])->name('rkt.truncate');

Route::get('/scaler', [ScalerController::class, 'index'])->name('scaler');
Route::post('/scaler/store', [ScalerController::class, 'store'])->name('scaler.store');
Route::patch('/scaler/{key}/update', [ScalerController::class, 'update'])->name('scaler.update');
Route::delete('/scaler/{key/delete}', [ScalerController::class, 'destroy'])->name('scaler.destroy');
Route::delete('/scaler/truncate', [ScalerController::class, 'truncate'])->name('scaler.truncate');

Route::get('/supervisor', [SupervisorController::class, 'index'])->name('supervisor');
Route::post('/supervisor/store', [SupervisorController::class, 'store'])->name('supervisor.store');
Route::patch('/supervisor/{key}/update', [SupervisorController::class, 'update'])->name('supervisor.update');
Route::delete('/supervisor/{key/delete}', [SupervisorController::class, 'destroy'])->name('supervisor.destroy');
Route::delete('/supervisor/truncate', [SupervisorController::class, 'truncate'])->name('supervisor.truncate');

Route::get('/user', [UserController::class, 'index'])->name('user');
Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
Route::patch('/user/{id}/update', [UserController::class, 'update'])->name('user.update');
Route::delete('/user/{id/delete}', [UserController::class, 'destroy'])->name('user.destroy');
Route::delete('/user/truncate', [UserController::class, 'truncate'])->name('user.truncate');

// Routing User page   
Route::get('user/activity', [UserController::class , 'userActivity'])
    ->name('activity');
Route::get('user/activity/detail/{id}', [UserController::class , 'getActivity'])
    ->name('activity.detail');
Route::post('user/activity/read_all', [UserController::class , 'readAll'])
    ->name('activity.read');