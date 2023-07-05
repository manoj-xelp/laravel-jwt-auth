<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login',[UserController::class,'login']);
        Route::post('register',[UserController::class,'create']);
        Route::get('verify_email',[UserController::class,'verifyUser']);
        Route::get('welcome_user', [UserController::class,'welcomessss']);
        Route::post('refresh_token', [UserController::class,'refresh']);
        Route::group(['middleware' => ['validateuser']], function(){
            Route::get('user/info', [UserController::class,'getUserInfo']);
        });
    });
});
