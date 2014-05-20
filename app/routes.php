<?php

//Simple route with closure
Route::get('/', ['as' => 'home', function()
{
    return View::make('home');
}]);

//Explicit routes mapped to controller methods
Route::get('login', ['as' => 'login', 'uses' => 'SessionsController@create']);
Route::post('login', 'SessionsController@store');

Route::get('logout', ['as' => 'logout', 'uses' => 'SessionsController@destroy']);
