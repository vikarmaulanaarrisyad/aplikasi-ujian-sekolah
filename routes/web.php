<?php

use App\Http\Controllers\{
    AcademicYearController,
    DashboardController,
    SchoolClassController
};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/academic-years/data', [AcademicYearController::class, 'data'])->name('academic-years.data');
    Route::resource('academic-years', AcademicYearController::class)->except('create', 'edit');

    Route::get('schoolClasses/data', [SchoolClassController::class, 'data'])->name('schoolClasses.data');
    Route::resource('schoolClasses', SchoolClassController::class)->except('create', 'edit');
});
