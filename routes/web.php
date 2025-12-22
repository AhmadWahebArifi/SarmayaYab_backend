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
