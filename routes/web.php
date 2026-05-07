<?php

use App\Http\Controllers\MenuWidgetAdminController;
use App\Http\Controllers\MenuWidgetController;
use App\Http\Controllers\ToastMenuController;
use Illuminate\Support\Facades\Route;

Route::get('/menu-widget', [MenuWidgetController::class, 'index']);
Route::get('/menu-widget-admin', [MenuWidgetAdminController::class, 'edit'])->name('menu-widget.admin.edit');
Route::post('/menu-widget-admin', [MenuWidgetAdminController::class, 'update'])->name('menu-widget.admin.update');
Route::post('/menu-widget-admin/fetch-new-menu', [MenuWidgetAdminController::class, 'fetchNewMenu'])->name('menu-widget.admin.fetch');
Route::get('/force-sync-menu', [ToastMenuController::class, 'forceSyncMenu']);
Route::get('/menu-json', [ToastMenuController::class, 'publicMenu']);
