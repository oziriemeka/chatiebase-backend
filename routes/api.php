<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\OrganizationSettingsController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamMembersController;
use App\Http\Controllers\UserApplicationSettingsController;
use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConversationController;

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

    Route::group(['prefix' => 'dashboard'], function () {
        Route::group(['prefix' => 'profile'], function () {
            Route::post('/update/account', [ProfileController::class, 'updateAccount']);
            Route::post('/update/password', [ProfileController::class, 'updatePassword']);
            Route::post('/update/avatar/upload', [ProfileController::class, 'uploadAvatar']);
            Route::post('/update/avatar/remove', [ProfileController::class, 'removeAvatar']);
            Route::post('/update/avatar/use-random', [ProfileController::class, 'useRandomAvatar']);
        });

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

        Route::group(['prefix' => 'widget'], function () {
            Route::get('/get', [WidgetController::class, 'getWidget']);
            Route::post('/update', [WidgetController::class, 'updateWidget']);
        });

        Route::group(['prefix' => 'application-settings'], function () {
            Route::get('/get', [UserApplicationSettingsController::class, 'getUserApplicationSettings']);
            Route::post('/update', [UserApplicationSettingsController::class, 'updateUserApplicationSettings']);
        });

        Route::group(['prefix' => 'organization-settings'], function () {
            Route::get('/get', [OrganizationSettingsController::class, 'getOrganizationSettings']);
            Route::post('/update', [OrganizationSettingsController::class, 'updateOrganizationSettings']);
        });

        Route::group(['prefix' => 'contacts'], function () {
            Route::get('/get', [ContactsController::class, 'getContacts']);
        });

        Route::group(['prefix' => 'conversation'], function () {
            Route::get('/get/{conversationId}', [ConversationController::class, 'getConversation']);
            Route::Delete('/delete/{conversationId}/message/{messageId}', [ConversationController::class, 'deleteConversationMessage']);
            Route::post('/send/{conversationId}', [ConversationController::class, 'sendConversationMessage']);
        });
    });
});
Route::get('/check', [AuthController::class, 'check']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
