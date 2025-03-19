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

// Health check route for Vercel
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Basic route to test the setup
Route::get('/', function () {
    return view('welcome');
});

// Add this route to check environment status
Route::get('/env-check', function () {
    return [
        'app_url' => config('app.url'),
        'app_env' => config('app.env'),
        'session_driver' => config('session.driver'),
        'secure_cookie' => config('session.secure'),
        'same_site' => config('session.same_site'),
    ];
});

Route::resource('tasks', TaskController::class);

// Fallback route for handling 404s
Route::fallback(function () {
    return response()->json(['error' => 'Not Found'], 404);
});
