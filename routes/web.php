<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [HomeController::class, 'index'])->name('home');


Route::controller(AccountController::class)->group(function () {

    //Guest Routes
    Route::group(['middleware' => 'guest'], function () {
        Route::get('/account/register', 'registration')->name('account.registration');
        Route::post('/account/process-register', 'processRegistration')->name('account.processRegistration');
        Route::get('/account/login', 'login')->name('account.login');
        Route::post('/account/authinticate', 'authenticate')->name('account.authenticate');
    });

    //Auth Routes
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/account/profile', 'profile')->name('account.profile');
        Route::get('/account/logout', 'logout')->name('account.logout');
    });
});
