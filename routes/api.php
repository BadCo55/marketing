<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfficeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/offices', [OfficeController::class, 'getOffices'])->name('api.offices.all');
Route::get('/all-users', [UserController::class, 'getUsers'])->name('api.users.all');