<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('login',[UserController::class,'login']);
Route::post('create',[UserController::class,'create']);
Route::get('verify',[UserController::class,'verifyUser']);
Route::get('welcome', [UserController::class,'welcomessss']);
Route::post('refresh', [UserController::class,'refresh']);
Route::group(['middleware' => ['validateuser']], function(){
    Route::get('welcome1', [UserController::class,'welcomessss']);
});