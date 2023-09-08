<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthorityController;
use App\Http\Controllers\Api\LogoController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\CertificateController;

Route::group(['prefix' => 'v1'], function () {

    // Rutas de Auth
    Route::post('auth/register', [AuthController::class, 'register'])->name('register');
    Route::post('auth/login', [AuthController::class, 'login'])->name('login');
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('logout')->middleware('jwt.auth');
    // Rutas para usuarios
    Route::resource('users', UserController::class)->except(['create', 'edit', 'store']);
    
    // Rutas protegidas por el middleware jwt.auth
    Route::group(['middleware' => 'jwt.auth'], function () {
        // Rutas para logos        
        Route::resource('images', LogoController::class)->except(['edit','create','destroy']);
        // Ruta para enviar token para restaurar la contraseña
        Route::post('sendPasswordResetLink', 'App\Http\Controllers\Api\PasswordResetRequestController@sendEmail');
        //Guardar nueva contraseña
        Route::post('resetPassword', 'App\Http\Controllers\Api\ChangePasswordController@passwordResetProcess');
    });
});
