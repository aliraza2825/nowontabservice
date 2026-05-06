<?php

use App\Http\Controllers\MenuWidgetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToastMenuController;

Route::get('/menu-widget', [MenuWidgetController::class, 'index']);
Route::get('/force-sync-menu', [ToastMenuController::class, 'forceSyncMenu']);
