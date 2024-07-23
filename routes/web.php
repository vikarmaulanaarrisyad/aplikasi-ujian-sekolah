<?php

use App\Http\Controllers\{
    AcademicYearController,
    DashboardController
};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/academic-years/data', [AcademicYearController::class, 'data'])->name('academic-years.data');
    Route::resource('academic-years', AcademicYearController::class)->except('create', 'edit');
});
