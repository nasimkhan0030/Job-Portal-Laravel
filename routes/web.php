<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [HomeController::class, 'index'])->name('home');


Route::prefix('account')->controller(AccountController::class)->group(function () {

    //Guest Routes
    Route::group(['middleware' => 'guest'], function () {
        Route::get('/register', 'registration')->name('account.registration');
        Route::post('/process-register', 'processRegistration')->name('account.processRegistration');
        Route::get('/login', 'login')->name('account.login');
        Route::post('/authinticate', 'authenticate')->name('account.authenticate');
    });

    //Auth Routes
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/profile', 'profile')->name('account.profile');
        Route::put('/update-profile', 'updateProfile')->name('account.updateProfile');
        Route::get('/logout', 'logout')->name('account.logout');
        Route::post('/update-profile-pic', 'updateProfilepic')->name('account.updateProfilepic');
    });
});
