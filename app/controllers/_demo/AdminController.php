<?php

use Models\Post;

class AdminController extends \BaseController
{
	public function getIndex()
	{
		$posts = Post::count();

		return ($posts > 0) ? Redirect::to('admin/posts') : View::make('admin.index');
	}
}