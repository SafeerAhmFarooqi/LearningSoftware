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
    return view('pages.application.index');
    //return view('pages.application.shooting-game');
    //return view('pages.application.shooting-game2');
});

Route::get('/json-response', function () {

    return collect([
        'id' => 1,
        'name' => 'Safeer',
        'age' => 35,
    ])->toJson();
})->name('json.response');
