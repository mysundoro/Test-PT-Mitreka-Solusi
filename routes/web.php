<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [App\Http\Controllers\Frontend\HomeController::class, 'index'])->name('home');

Route::post('/language/change', [App\Http\Controllers\LanguageController::class, 'change'])->name('language.change');

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::group(['prefix' => 'panel'], function() {
        Route::get('/dashboard', [App\Http\Controllers\Backend\DashboardController::class, 'index'])->name('dashboard');

        // Notification
        Route::group(['prefix' => 'notification'], function() {
            Route::get('/', [App\Http\Controllers\Backend\NotificationController::class, 'getNotification'])->name('notification.index');
            Route::patch('/read', [App\Http\Controllers\Backend\NotificationController::class, 'markAsRead'])->name('notification.markAsRead');
            Route::delete('/clear', [App\Http\Controllers\Backend\NotificationController::class, 'clear'])->name('notification.clear');
        });

        // Setting Module
        Route::group(['prefix' => 'setting', 'middleware' => ['permission:Setting']], function() {
            Route::get('/roles/data', [App\Http\Controllers\Backend\Setting\RoleController::class, 'data'])->name('roles.data');
            Route::resource('roles', App\Http\Controllers\Backend\Setting\RoleController::class);

            Route::get('/users/data', [App\Http\Controllers\Backend\Setting\UserController::class, 'data'])->name('users.data');
            Route::resource('users', App\Http\Controllers\Backend\Setting\UserController::class);

            Route::get('/menu/getMenu/{category}', [App\Http\Controllers\Backend\Setting\MenuController::class, 'getMenu'])->name('menu.getMenu');
            Route::resource('menu', App\Http\Controllers\Backend\Setting\MenuController::class);

            Route::resource('configuration', App\Http\Controllers\Backend\Setting\ConfigurationController::class);

            Route::resource('language', App\Http\Controllers\Backend\Setting\LanguageController::class);

            Route::get('/language_code/data', [App\Http\Controllers\Backend\Setting\LanguageCodeController::class, 'data'])->name('language_code.data');
            Route::post('/language_code/{code}/create', [App\Http\Controllers\Backend\Setting\LanguageCodeController::class, 'createLanguage'])->name('language_code.create_language');
            Route::resource('language_code', App\Http\Controllers\Backend\Setting\LanguageCodeController::class);

            Route::get('/permissions/data', [App\Http\Controllers\Backend\Setting\PermissionController::class, 'data'])->name('permissions.data');
            Route::resource('permissions', App\Http\Controllers\Backend\Setting\PermissionController::class);
        });

        Route::get('/getRealtimePrice/{symbol}', [App\Http\Controllers\Backend\RealtimePriceController::class, 'getRealtimePrice'])->name('realtime.getRealtimePrice');
    });
});
