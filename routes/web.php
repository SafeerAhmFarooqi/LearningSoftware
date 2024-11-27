<?php

use App\Http\Controllers\DateRangeController;
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
    return view('welcome');
});

Route::get('/date-range-picker', function () {
    return view('date-range-picker');
});

Route::get('/date-range-picker-2', function () {
    return view('date-range-picker-2');
});

Route::post('/date-range-picker', [DateRangeController::class, 'submit'])->name('booking.date');
