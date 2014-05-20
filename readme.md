Laravel Tutorial for PHPTek

presented by Collin Schneider (Think Say Do, LLC)

#ROUTING

	Route::group(array('before' => 'auth'), function()
	{
    	Route::get('/', function()
    	{
        	//has auth filter
    	});

	});

//can also route to subdomains

resourceful routes; built for crud operations; name your methods to the standard resource/method name


#Controller

