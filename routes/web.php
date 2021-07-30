<?php

use App\Mail\ConfirmWithPin;
use Illuminate\Support\Facades\Mail;
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

Route::get('/', function () {
    return view('welcome');
});

// for testing an email
// Route::get('/mailable', function () {
    // preview
    // return new ConfirmWithPin();

    // send email
    // Mail::to(['leiyu876@gmail.com'])
    //     ->send(new ConfirmWithPin('newusername', mt_rand(100000, 999999)));
// });
