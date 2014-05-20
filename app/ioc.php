<?php
/*
// Quick IoC demo

interface ResponseInterface {}

class DemoResponse extends \Illuminate\Support\Facades\Response implements ResponseInterface {}

class RedResponse extends \Illuminate\Support\Facades\Response implements ResponseInterface
{
	public static function make($content = '', $status = 200, array $headers = array())
	{
		return parent::make('<span style="color: red;">' . $content . '</span>', $status, $headers);
	}
}

class Hello
{
	protected $response = null;

	function __construct(ResponseInterface $response)
	{
		$this->response = $response;
	}

	public function say($what = 'Hello World')
	{
		return $this->response->make($what);
	}
}

App::bind('ResponseInterface', 'RedResponse');

$router = App::make('router');

$router->get('/', function()
{
	global $app;

	return $app
		->make('hello')
		->say('Welcome to php[tek] 2014!');
});
*/