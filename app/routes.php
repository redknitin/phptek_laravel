<?php

//Simple route with closure
Route::get('/', ['as' => 'home', function()
{
    return Response::view('home');
}]);