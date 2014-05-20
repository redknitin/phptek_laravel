<?php

//Simple route with closure
Route::get('/', ['as' => 'home', function()
{
    return View::make('home');
}]);