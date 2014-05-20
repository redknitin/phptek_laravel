<?php

class BaseController extends Controller
{
	public function __construct()
	{
		if (Session::has('error')) View::share('error', Session::get('error'));
		if (Session::has('message')) View::share('message', Session::get('message'));
	}
}
