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

Route::get('/', function () {
    return response()->json([
        'message' => 'SarmayaYab API is running!',
        'status' => 'active',
        'version' => '1.0.0'
    ]);
});

Route::get('/test', function () {
    return 'Simple test route works!';
});

// Serve React SPA - catch all other routes and return the React app
Route::get('/{any}', function () {
    return response()->json([
        'message' => 'React frontend should be served here',
        'note' => 'Configure your web server to serve the React build from public directory'
    ]);
})->where('any', '.*');
