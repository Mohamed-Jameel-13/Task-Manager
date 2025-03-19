<?php

use App\Http\Controllers\TaskController;
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

Route::get('/', function () {
    return redirect()->route('tasks.index');
});

// Wrap task routes with middleware that disables CSRF protection when on Vercel
Route::middleware(env('VERCEL_ENV') ? ['web', 'session'] : ['web'])->group(function () {
    Route::resource('tasks', \App\Http\Controllers\TaskController::class);
});
