<?php

use App\Http\Controllers\MenuWidgetAdminAuthController;
use App\Http\Controllers\MenuWidgetAdminController;
use App\Http\Controllers\MenuWidgetController;
use App\Http\Controllers\ToastMenuController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MenuWidgetAdminAuthController::class, 'showLogin'])->name('menu-widget.admin.login');
Route::post('/', [MenuWidgetAdminAuthController::class, 'login'])->name('menu-widget.admin.login.submit');
Route::get('/menu-widget', [MenuWidgetController::class, 'index']);
Route::get('/menu-widget-admin/login', [MenuWidgetAdminAuthController::class, 'showLogin']);
Route::post('/menu-widget-admin/login', [MenuWidgetAdminAuthController::class, 'login']);
Route::post('/menu-widget-admin/logout', [MenuWidgetAdminAuthController::class, 'logout'])->name('menu-widget.admin.logout');

Route::middleware('menu-widget-admin')->group(function () {
    Route::get('/menu-widget-admin', [MenuWidgetAdminController::class, 'edit'])->name('menu-widget.admin.edit');
    Route::post('/menu-widget-admin', [MenuWidgetAdminController::class, 'update'])->name('menu-widget.admin.update');
    Route::post('/menu-widget-admin/fetch-new-menu', [MenuWidgetAdminController::class, 'fetchNewMenu'])->name('menu-widget.admin.fetch');
    Route::get('/force-sync-menu', [ToastMenuController::class, 'forceSyncMenu']);
});

Route::get('/menu-json', [ToastMenuController::class, 'publicMenu']);
