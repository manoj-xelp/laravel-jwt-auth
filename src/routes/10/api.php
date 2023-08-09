<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StaticDataController;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login',[UserController::class,'login']);
        Route::post('register',[UserController::class,'create']);
        Route::get('verify_email',[UserController::class,'verifyUser']);
        Route::get('welcome_user', [UserController::class,'welcomessss']);
        Route::post('forgot_password', [UserController::class,'forgotPassword']);
        Route::get('reset_password', [UserController::class,'resetPasswordValidation']);
        Route::post('reset_password/change', [UserController::class,'resetPassword']);
        Route::group(['middleware' => ['validateuser']], function(){
            Route::post('refresh_token', [UserController::class,'refresh']);
            Route::post('logout', [UserController::class,'logout']);
            Route::post('change_password', [UserController::class,'changePassword']);
        });
    });
    Route::prefix('users')->group(function () {
        Route::group(['middleware' => ['validateuser']], function () {
            Route::get('info', [UserController::class,'getUserInfo']);
            Route::put('update', [UserController::class,'updateUser']);
        });
    });
    Route::prefix('files')->group(function () {
        Route::group(['middleware' => ['validateuser']], function () {
            Route::post('upload_single', [StaticDataController::class,'uploadSingleFile']);
            Route::post('upload_multiple', [StaticDataController::class,'uploadMultiFiles']);
        });
    });
    Route::prefix('static')->group(function () {
        Route::group(['middleware' => ['validateuser']], function () {
            Route::get('countries', [StaticDataController::class,'getCountries']);
            Route::get('states', [StaticDataController::class,'getStates']);
            Route::get('cities', [StaticDataController::class,'getCities']);
        });
    });


});
