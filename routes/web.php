<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::resource('/', HomeController::class, ['only' => ['index', 'create']]);
Route::resource('video', VideoController::class, ['except' => ['edit', 'update', 'destroy']]);
Route::get('video/{video}/convert', [VideoController::class, 'convert']);
Route::get('video/{video}/download', [VideoController::class, 'download'])->name('video.download');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return \Inertia\Inertia::render('Dashboard');
})->name('dashboard');
