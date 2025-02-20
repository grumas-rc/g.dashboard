<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [\App\Http\Controllers\DomainStatisticsController::class, 'getDomains'])->name('stats');
Route::get('/domain/{domain_id}', [\App\Http\Controllers\DomainStatisticsController::class, 'getDomain'])->name('domain.stats')->where(['domain_id' => '[0-9]+']);
Route::get('/andrew', [\App\Http\Controllers\DomainStatisticsController::class, 'getDomainsList'])->name('get-domains-list');
//Route::get('/avatars', [\App\Http\Controllers\DomainStatisticsController::class, 'avatars'])->name('avatars');
