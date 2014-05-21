<?php

//Simple route with closure
Route::get('/', ['as' => 'home', function()
{
    $posts = new Models\Post;

    $posts = $posts->orderBy('id', 'desc');
    $posts = $posts->published();

    if (Input::has('tag'))
    {
        $tag = Input::get('tag');
        $posts = $posts->withTag($tag);
    }
    else
    {
        $tag = null;
    }

    $posts = $posts->paginate(5);

    return View::make('home', compact('posts', 'tag'));
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
