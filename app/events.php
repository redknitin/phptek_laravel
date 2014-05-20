<?php

App::error(function(Exception $exception, $code)
{
	Log::info(Request::getSchemeAndHttpHost() . Request::getRequestUri());
	Log::error($exception);

	if ( ! Config::get('app.debug')) return Response::view('_shared.error', [], $code);
});

App::missing(function($exception)
{
	return Response::view('_shared.missing', [], 404);
});

App::down(function()
{
	return Response::view('_shared.down', [], 503);
});