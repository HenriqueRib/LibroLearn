<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\RegistrationController;

Route::get('/home', [ClientController::class, 'home'])->name('home');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/me', [AuthController::class, 'me']);

Route::group(['prefix' => 'student', 'middleware' => 'auth:api'], function () {
    Route::get('/all', [StudentController::class, 'all']);
    Route::post('/create', [StudentController::class, 'create']);
    Route::get('/{id}', [StudentController::class, 'show']);
    Route::put('/{id}/update', [StudentController::class, 'update']);
    Route::delete('/delete', [StudentController::class, 'delete']);

    Route::post('/search', [StudentController::class, 'search']);
    Route::get('/', [StudentController::class, 'index']);
    
});

Route::group(['prefix' => 'course', 'middleware' => 'auth:api'], function () {
    Route::get('/all', [CourseController::class, 'all']);
    Route::post('/create', [CourseController::class, 'create']);
    Route::get('/{id}', [CourseController::class, 'show']);
    Route::put('/{id}/update', [CourseController::class, 'update']);
    Route::delete('/delete', [CourseController::class, 'delete']);
});

Route::group(['prefix' => 'registration', 'middleware' => 'auth:api'], function () {
    Route::get('/all', [RegistrationController::class, 'all']);
    Route::post('/create', [RegistrationController::class, 'create']);
    Route::get('/{id}', [RegistrationController::class, 'show']);
    Route::put('/{id}/update', [RegistrationController::class, 'update']);
    Route::delete('/delete', [RegistrationController::class, 'delete']);
});