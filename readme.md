Laravel Tutorial for PHPTek

presented by Collin Schneider (Think Say Do, LLC)

[slides]( http://thinksaydo.com/phptek-laravel.pdf)

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

#Input

#views

{{{ escapes vs. {{ 

#Traits

extending class; in traits a way of packaging the methods you are using without extending them and mixing them into your classes.  What is the difference between that and helper methods wrt usage?

#Regenerate the key

	> php artisan key:generate
	
	should put the new key in `app/app.php`
	
	
#Migration

	> php artisan generate:migration create_posts_table --fields="name:string, body:text, slug:string:nullable, tags:string:nullable, string:status"

