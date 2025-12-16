<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::resource('schools', App\Http\Controllers\SchoolController::class);
    Route::resource('students', App\Http\Controllers\StudentController::class);
    Route::resource('id-card-templates', App\Http\Controllers\IdCardTemplateController::class);
    Route::get('id-card-templates/{id_card_template}/generate', [App\Http\Controllers\IdCardTemplateController::class, 'generate'])->name('id-card-templates.generate');
    Route::post('id-card-templates/{id_card_template}/toggle-status', [App\Http\Controllers\IdCardTemplateController::class, 'toggleStatus'])->name('id-card-templates.toggle-status');

    // Profile Routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'password'])->name('profile.password');
});
