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

First, If you don't have any twitter application, you will need to register an application from [Twitter Developer](https://dev.twitter.com/). 

**Be careful! After you create your application don't forget to set your Callback URL under [My Applications](https://dev.twitter.com/apps) > (Select Your Application) > Settings > Callback URL**

Next, grab your consumer key and secret codes, create `twitter.php` configuration file into your `fuel/app/config` directory. Here is a sample configuration file:

```php
return array(
	'production' => array(
		// twitter api consumer configurations 
		'consumer_key'               => 'SET_HERE_YOUR_CONSUMER_KEY',
		'consumer_secret'            => 'SET_HERE_YOUR_CONSUMER_SECRET',
	),

	'development' => array(
		// twitter api consumer configurations 
		'consumer_key'               => 'SET_HERE_YOUR_CONSUMER_KEY',
		'consumer_secret'            => 'SET_HERE_YOUR_CONSUMER_SECRET',
	),
);
```

If you are gonna use twitter for personal stuff (for a static user) you can also declare your user access tokens for spesific enviroments. Your config file should be like this;

```php
'production' => array(
	// twitter api consumer configurations 
	'consumer_key'               => 'SET_HERE_YOUR_CONSUMER_KEY',
	'consumer_secret'            => 'SET_HERE_YOUR_CONSUMER_SECRET',
	
	// user 
	'user_token'                 => 'SET_HERE_YOUR_ACCESS_TOKEN',
	'user_secret'                => 'SET_HERE_YOUR_ACCESS_SECRET',
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

