<?php 


Route::post('user', 'UserController@create');
Route::post('login', 'UserController@login');

Route::group(['middleware' => ['validateuser']], function(){
    Route::get('welcome1', 'UserController@welcomessss');
});