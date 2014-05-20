<?php

class SessionsController extends \BaseController
{
	public function create()
	{
		return View::make('sessions.create');
	}

	public function store()
	{
		$credentials = Input::only(['username', 'password']);

		$credentials['status'] = 'enabled';

		if (Auth::attempt($credentials, Input::has('remember')))
		{
			return Redirect::to('admin')
				->with('message', 'Welcome ' . Auth::user()->name . '!');
		}

		return Redirect::back()
			->with('error', 'Invalid login.');
	}

	public function destroy($id = null)
	{
		Auth::logout();

		return Redirect::route('login')
			->with('message', 'Logged out.');
	}
}