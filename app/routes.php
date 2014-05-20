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

// Add the admin route
Route::group(['before' => 'auth'], function()
{
    //Defer entire route to resource controller
    Route::resource('admin/posts', 'PostsController');

    //Defer entire route to controller
    Route::controller('admin', 'AdminController');
});

//Catch all blog post slugs
Route::get('/{slug}', ['uses' => 'PostsController@show']);
