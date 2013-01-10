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
  $ git clone https://github.com/tevfik6/fuel-twitter twitter
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

## Utilities

#### Twitter::user()
Alias for Twitter::get('1.1/account/verify_credentials');
```php
$user = Twitter::user();
```

***

#### Twitter::timeline($timeline = "home", $params = array())
Get specific time line status
```php
//gets last 10 status from home_timeline
$home = Twitter::timeline("home", array(
	'count'  => 10
));

//gets user_timeline with twitter defaul parameters
$user = Twitter::timeline("user");

//gets mentions after status ID 12345 
$mentions = Twitter::timeline("mentions", array(
	'max_id' => 12345
));
```
More info about parameters, check [Twitter statuses/home_timeline], [Twitter statuses/user_timeline], [Twitter statuses/mentions_timeline]

***

#### Twitter::update($params = array())
Alias for Updates the authenticating user's current status
```php
$status = Twitter::update("This is an another status.");

//or you can use with parameters
$status = Twitter::update(array(
	'status'    => "This is an another status.",
	'trim_user' => 1
));
```
More info about parameters, check [Twitter statuses/update]

***

#### Twitter::destroy($id)
Destroys the status specified by the required ID parameter.
```php
$status = Twitter::destroy(123456789);
```

***

#### Twitter::search($params = array())
Returns a collection of relevant Tweets matching a specified query.
```php
$search_results = Twitter::search("SEARCH_KEY");

//or you can use with parameters
$search_results = Twitter::search(array(
	'q'     => SEARCH_KEY,
	'count' => 10
));
```
More info about parameters, check [Twitter search/tweets]
***

#### Twitter::char_size($status = '')
Give the exact character size of status
```php
$char_size = Twitter::char_size("café");
$strlen    = strlen("café");
echo "café: " . $char_size . " != " . $strlen;
// result: cafÃ©: 4 != 5
```
More info about counting characters, check [Twitter Counting characters]

***

#### Twitter::safe_char_size($status = '')
Give the exact character size status with links
```php
$status         = "Test status: café https://github.com/tevfik6/fuel-twitter";
$safe_char_size = Twitter::safe_char_size($status);
$strlen         = strlen($status);
echo $status." => " . $safe_char_size . " != " . $strlen;
//result:
//		Test status: cafÃ© https://github.com/tevfik6/fuel-twitter => 39 != 58
```
More info about conversition; check [Twitter How do I calculate if a Tweet with a link is going to be over 140 characters or not?]



[Twitter Developer]: https://dev.twitter.com/
[My Applications]: https://dev.twitter.com/apps
[Twitter API Resources]: https://dev.twitter.com/docs/api/1
[Twitter - OAuth/request_token]: https://dev.twitter.com/docs/api/1/post/oauth/request_token
[Twitter statuses/home_timeline]: https://dev.twitter.com/docs/api/1.1/get/statuses/home_timeline
[Twitter statuses/user_timeline]: https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
[Twitter statuses/mentions_timeline]: https://dev.twitter.com/docs/api/1.1/get/statuses/mentions_timeline
[Twitter statuses/update]: https://dev.twitter.com/docs/api/1.1/post/statuses/update
[Twitter search/tweets]: https://dev.twitter.com/docs/api/1.1/get/search/tweets
[Twitter Counting characters]:https://dev.twitter.com/docs/counting-characters#Definition_of_a_Character
[Twitter How do I calculate if a Tweet with a link is going to be over 140 characters or not?]:https://dev.twitter.com/docs/tco-link-wrapper/faq#How_do_I_calculate_if_a_Tweet_with_a_link_is_going_to_be_over_140_characters_or_not