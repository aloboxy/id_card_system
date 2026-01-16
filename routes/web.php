<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    // General Routes
    Route::resource('students', App\Http\Controllers\StudentController::class);
    Route::post('/students/{student}/update-photo', [App\Http\Controllers\StudentController::class, 'updatePhoto'])->name('students.update-photo');

    // Admin Only Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', App\Http\Controllers\UserController::class);
        Route::resource('roles', App\Http\Controllers\RoleController::class);
        Route::resource('schools', App\Http\Controllers\SchoolController::class);
        Route::resource('staff', App\Http\Controllers\StaffController::class);
        Route::resource('id-card-templates', App\Http\Controllers\IdCardTemplateController::class);
        Route::get('id-card-templates/{id_card_template}/generate', [App\Http\Controllers\IdCardTemplateController::class, 'generate'])->name('id-card-templates.generate');
        Route::post('id-card-templates/{id_card_template}/toggle-status', [App\Http\Controllers\IdCardTemplateController::class, 'toggleStatus'])->name('id-card-templates.toggle-status');

        // System Settings
        Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
    });

    // Profile Routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'password'])->name('profile.password');
});
