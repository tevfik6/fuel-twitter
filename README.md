# FuelPHP Twitter API Package

This is a simple, easy to use Twitter API Package.

## About

- Version: 1.0
- License: MIT License
- Author: Tevfik TÜMER
- Author of tmhOAuth: Matt Harris

## Installation

You can simply download the package and extract it into `fuel/packages/twitter` folder or you can go under your `fuel/packages` folder and run this command:
```shell
  $ git clone http://github.com/tevfik6/fuel-twitter.git twitter
```

## Configuration

First, If you don't have any twitter application, you will need to register an application from [Twitter Developer]. 

**Be careful! After you create your application don't forget to set your Callback URL under [My Applications] > (Select Your Application) > Settings > Callback URL**

Next, grab your consumer key and secret codes, create `twitter.php` configuration file into your `fuel/app/config` directory. Here is a sample configuration file:

```php
return array(
	'production' => array(
		// twitter api consumer configurations 
		'consumer_key'    => 'SET_HERE_YOUR_CONSUMER_KEY',
		'consumer_secret' => 'SET_HERE_YOUR_CONSUMER_SECRET',
	),

	'development' => array(
		// twitter api consumer configurations 
		'consumer_key'    => 'SET_HERE_YOUR_CONSUMER_KEY',
		'consumer_secret' => 'SET_HERE_YOUR_CONSUMER_SECRET',
	),
);
```

If you are gonna use twitter for personal stuff (for a static user) you can also declare your user access tokens for spesific enviroments. Your config file should be like this;

```php
'production' => array(
	// twitter api consumer configurations 
	'consumer_key'    => 'SET_HERE_YOUR_CONSUMER_KEY',
	'consumer_secret' => 'SET_HERE_YOUR_CONSUMER_SECRET',
	
	// user 
	'user_token'      => 'SET_HERE_YOUR_ACCESS_TOKEN',
	'user_secret'     => 'SET_HERE_YOUR_ACCESS_SECRET',
),

```

## Basic usage

```php
if ( ! Twitter::logged_in() ){
	Twitter::login();
}

$user = Twitter::user();

if ( ! $user )
{
	$user = Twitter::error_message();
}
echo "<pre>";
print_r($user);
echo "</pre>";
```

## Common Methods

#### Twitter::logged_in()
Check user currently logged in or not 
```php
if ( Twitter::logged_in() )
{
	echo "User logged in."
}
```

***

#### Twitter::login($params = array())
Try to create session given configuration with parameters. You can check for the params from [Twitter - OAuth/request_token]
```php
if( ! Twitter::logged_in() )
{
	Twitter::login(array(
		'x_auth_access_type' => "write"
	));
}
```

***

#### Twitter::logout()
Basically clear Twitter variables from session;
```php
Twitter::logout();
```

#### Twitter::set_tokens($tokens)
Set user access tokens
```php
Twitter::set_tokens(array(
	'user_token'  => 'SET_HERE_YOUR_ACCESS_TOKEN',
	'user_secret' => 'SET_HERE_YOUR_ACCESS_SECRET',
));
```

***

#### Twitter::get_tokens()
Get user access tokens
```php
$user_tokens = Twitter::get_tokens();
echo "<pre>";
print_r($user_tokens);
echo "</pre>";
/**
 * Array
 * (
 *  	[user_token]  => USER_ACCESS_TOKEN
 *  	[user_secret] => USER_ACCESS_SECRET
 * )
 */
```

***

#### Twitter::get($resource, $params = array())
GET request to given resource with parameters. To get more information about Resources and Parameters follow [Twitter API Resources].
```php
$user = Twitter::get('1.1/account/verify_credentials');
```

***

#### Twitter::post($resource, $params = array())
POST request to given resource with parameters. To get more information about Resources and Parameters follow [Twitter API Resources].
```php
$status = Twitter::post("1.1/statuses/update", array(
	'status' => 'This is a supper dupper status! sent via FuelPHP Twitter Package'
));
```

[Twitter Developer]: https://dev.twitter.com/
[My Applications]: https://dev.twitter.com/apps
[Twitter API Resources]: https://dev.twitter.com/docs/api/1
[Twitter - OAuth/request_token]: https://dev.twitter.com/docs/api/1/post/oauth/request_token