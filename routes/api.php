<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\TeamMembersController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'api', 'throttle:60,1'], function(){
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('resetPassword');
    Route::get('/reset-password/check/{token}', [AuthController::class, 'resetPasswordCheckToken'])->name('resetPasswordCheckToken');
    Route::post('/reset-password/{token}', [AuthController::class, 'resetPasswordToken'])->name('resetPasswordToken');
});

//Route::group(['middleware' => 'api', 'throttle:60,1'], function(){
//});


Route::group(['middleware' => ['jwt.auth', 'canAccess']], function() {

    Route::get('/check', [AuthController::class, 'check']);

    ##Undone
    Route::group(['prefix' => 'dashboard'], function () {
        Route::group(['prefix' => 'permissions'], function () {
            Route::get('/get', [PermissionsController::class, 'getPermissions']);
            Route::get('/get-available-privilege', [PermissionsController::class, 'getAvailablePrivilege']);
            Route::post('/add', [PermissionsController::class, 'addPermissions']);
        });

        Route::group(['prefix' => 'privilege'], function () {
            Route::get('/get', [PermissionsController::class, 'getCustomPrivilege']);
            Route::post('/delete/{permissionId}', [PermissionsController::class, 'deleteCustomPrivilege']);
            Route::post('/edit/{permissionId}', [PermissionsController::class, 'editCustomPrivilege']);
        });

        Route::group(['prefix' => 'team'], function () {
            Route::post('/add', [TeamMembersController::class, 'addTeamMember']);
            Route::get('/get', [TeamMembersController::class, 'getTeamMember']);
            Route::post('/delete/{item}', [TeamMembersController::class, 'deleteTeamMember']);
            Route::post('/delete-selected', [TeamMembersController::class, 'deleteSelectedTeamMember']);
            Route::post('/edit/{item}', [TeamMembersController::class, 'editTeamMember']);
            Route::post('/search/', [TeamMembersController::class, 'searchTeamMember']);
        });
    });
});
