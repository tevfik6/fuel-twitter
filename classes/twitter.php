<?php
/**
 * FuelPHP Twitter API Package
 *
 * @copyright  2012 Tevfik TÜMER
 * @license    MIT License
 */

namespace Twitter;

class TwitterException extends \Exception {}

/**
 * TwitterOAuth Class for Twitter API class
 */
class TwitterOAuth extends \tmhOAuth{

	public function __construct()
	{
		//Loading twitter configurations
		$config = \Config::load('twitter', true);
		//fetchin active configurations
		$config_default = $config['default'];
		$config_active  = $config[$config['active']];

		if( ! isset( $config_active ['consumer_key'] ) || empty( $config_active ['consumer_key'] ) 
			|| ! isset( $config_active ['consumer_secret'] ) || empty( $config_active ['consumer_secret'] ) )
		{
			throw new TwitterException("Set your consumer_key and consumer_secret for ".$config['active']." configration!");
		}

		//merge defualt configuration and active enviroment configuration
		$config_active  = array_merge($config_default, $config_active);

		// set configuration defined by users 
		parent::__construct($config_active);
	}


	/**
	 * check user access token(logged in) or not
	 * 
	 * @return boolean 
	 */
	public function logged_in()
	{
		//get everything on session; if has not, will be null
		$twitter_access_token = \Session::get("twitter_access_token", null);

		//check is there any access
		if($twitter_access_token !== null)
		{
			//set user token and user secret key from twitter_access_token
			$this->config['user_token']  = $twitter_access_token['oauth_token'];
			$this->config['user_secret'] = $twitter_access_token['oauth_token_secret'];
			return true;
		}
		return false;
	}

	/**
	 * check is there any user access token set by user or in the session
	 */
	protected function has_access()
	{
		//get tokens from configuration
		$tokens = $this->get_tokens();
		//check the tokens are defined by user or still empty string in the configurations
		if ( ! empty($tokens['user_token']) && ! empty($tokens['user_secret']) ) 
		{
			$this->config['user_token']  = $tokens['user_token'];
			$this->config['user_secret'] = $tokens['user_secret'];
			return true;
		}

		return $this->logged_in();
	}

	/**
	 * get user access token
	 */
	protected function grant_access()
	{
		// check user has not access token
		if( ! $this->has_access() )
		{
			//get twitter_access_token on session; if has not, will be null
			$twitter_oauth = \Session::get("twitter_oauth", null);

			//set user token and user secret key from twitter_oauth
			$this->config['user_token']  = $twitter_oauth['oauth_token'];
			$this->config['user_secret'] = $twitter_oauth['oauth_token_secret'];

			//request an access with user_token and user_secret
			$this->request_access();
		}
	}

	/**
	 * get temproraly access token
	 * 
	 * @param  array  $params request token parameters follow the link for details 
	 *                        (https://dev.twitter.com/docs/api/1/post/oauth/request_token)
	 */
	public function login($params = array())
	{
		//get twitter_access_token on session; if has not, will be null
		$twitter_oauth = \Session::get("twitter_oauth", null);
		
		//check is there any access
		if($twitter_oauth === null)
		{
			//post a request for a temprorary to grant access token
			$this->request_token($params);	
		}

		//if there is a temproray access check for login
		$this->grant_access();
	}

	/**
	 * delete all granted access from session
	 */
	public function logout()
	{
		//delete temproraly oauth tokens
		\Session::delete("twitter_oauth");
		//delete oauth access tokens
		\Session::delete("twitter_access_token");
	}


	/**
	 * get alias for resource and parameters
	 *
	 * for Twitter API Resources follow link for more information
	 * (https://dev.twitter.com/docs/api/1)
	 * 
	 * @param  string $resource resource string
	 * @param  array  $params   resource parameters
	 * @return object|null      response of given resource
	 */
	public function get($resource, $params = array())
	{
		// check user has access token
		if ( $this->has_access() ) {
			//get resource
			$response_code = $this->request(
				'GET',
				$this->url($resource),
				$params
			);

			// check response_code ? 200 OK
			if ($response_code == 200)
			{
				//return all get response as object
				return json_decode($this->response['response']);
			}
			// if there is an error
			return null;
		}
	}


	/**
	 * post alias for resource and parameters
	 * 
	 * for Twitter API Resources follow link for more information
	 * (https://dev.twitter.com/docs/api/1)
	 * 
	 * @param  string $resource resource string
	 * @param  array  $params   resource parameters
	 * @return object|null      response of given resource
	 */
	public function post($resource, $params = array())
	{
		// check user has access token
		if ( $this->has_access() ) {
			//get params to given resource
			$response_code = $this->request(
				'POST',
				$this->url($resource),
				$params
			);
			// check response_code ? 200 OK
			if ($response_code == 200)
			{
				//return all post response as object
				return json_decode($this->response['response']);
			}
			// if there is an error
			return null;
		}
	}


	/**
	 * alias for user verify credentials (user details)
	 * 
	 * @return array|null 	user details or null
	 */
	public function user()
	{
		return $this->get('1.1/account/verify_credentials');
	}


	/**
	 * alias for timeline
	 * 
	 * @param  string $timeline home|user|mentions
	 * @param  array  $params   parameters(ie. count)
	 * @return array|null           
	 */
	public function timeline($timeline = "home", $params = array())
	{
		$whitelist = array("home", "user", "mentions");
		if ( in_array($timeline, $whitelist) )
		{
			return $this->get('1.1/statuses/'.$timeline.'_timeline', $params);
		}
		return null;
	}

	/**
	 * alias for update
	 * 
	 * @param  string $message tweet message
	 * @param  array  $params  parameters
	 * @return [type]          [description]
	 */
	public function update($params = array())
	{
		if( ! is_array( $params ) && is_string($params) )
		{
			$params = array(
				'status' => $params
			);
		}
		return $this->post("1.1/statuses/update", $params);
	}

	/**
	 * alias for destroy/delete a status
	 * 
	 * @param  longint $id status id
	 * @return object|string     
	 */
	public function destroy($id)
	{
		return $this->post("1.1/statuses/destroy/".$id);
	}


	/**
	 * alias for search in tweets
	 * 
	 * @param  array  $params search parameters
	 * @return object|string         
	 */
	public function search($params = array())
	{	
		$default_params = array(
			// 'result_type' => 'mixed'
		);

		if( ! is_array( $params ) && is_string($params) )
		{
			$params = array(
				'q' => $params
			);
		}
		$params = array_merge($default_params, $params);
		return $this->get("1.1/search/tweets", $params);
	}










	/**
	 * set defined tokens
	 * 
	 * @param array $tokens an array must contains user_token and user_secret
	 */
	public function set_tokens($tokens)
	{
		//checking is there any pre-defined user token and access
		if ( ( isset($tokens['user_token']) && ! empty($tokens['user_token']) )  
			&& ( isset($tokens['user_secret']) && ! empty($tokens['user_secret']) ) ) 
		{			
			$this->config['user_token']  = $tokens['user_token'];
			$this->config['user_secret'] = $tokens['user_secret'];
		}
	}

	/**
	 * get user tokens from configuration
	 * 
	 * @return array tokens
	 */
	public function get_tokens()
	{
		$tokens['user_token']  = $this->config['user_token'];
		$tokens['user_secret'] = $this->config['user_secret'];

		return $tokens;
	}

	/**
	 * get response message
	 * 
	 * @return array|null  an array which contains errors
	 */
	public function error_message()
	{	
		//check is there any response set and not empty
		if ( isset($this->response['response'])
			|| ! empty($this->response['response']) ) 
		{
			//if it's not a json return it.
			if ( ! $error = json_decode($this->response['response']) )
			{
				return $this->response['response']->errors;
			}
			//if it's a json send a json object
			return $error;
		}
		return null;
	}

	/**
	 * Step 1: Request a temporary token
	 * 
	 * get temproraly access token
	 * 
	 * @param  array  $params 	request token parameters follow the link for details
	 *         (https://dev.twitter.com/docs/api/1/post/oauth/request_token)
	 * @return boolean
	 */
	protected function request_token($params = array()) {
		//post paramaters to twitter/oauth/request_token
		$response_code = $this->request(
			'POST',
			$this->url('oauth/request_token', ''),
			$params
		);
		// check response_code ? 200 OK
		if ($response_code == 200) 
		{
			//get parameters from response
			$twitter_oauth       = $this->extract_params($this->response['response']);
			//write temproraly oauth tokens to session
			\Session::set("twitter_oauth", $twitter_oauth);
			//get oauth_token from response
			$oauth_token = $twitter_oauth['oauth_token'];
			//pass to authentication
			$this->authenticate($oauth_token);
		} 
		// if response_code not 200
		else 
		{
			return false;
		}
	}

	/**
	 * Step 2: Direct the user to the authentication web page
	 * 
	 * prepare url and redirect it to authentication page
	 * 
	 * @param  string $oauth_token  authentication token
	 */
	protected function authenticate($oauth_token) {
		//prepare the necessary url for authorization
		$authurl = $this->url("oauth/authenticate", '') .  "?oauth_token={$oauth_token}";
		//go twitter authorize page for authorization
		\Response::redirect($authurl);
	}


	/**
	 * Step 3: This is the code that runs when Twitter redirects the user to the callback. 
	 * Exchange the temporary token for a permanent access token
	 * 
	 * get permanent access tokens
	 * 
	 * @return boolean 
	 */
	protected function request_access() {
		//post oauth_verifier to twitter/oauth/access_token
		$response_code = $this->request(
			'POST',
			$this->url('oauth/access_token', ''),
			array(
				'oauth_verifier' => \Input::param('oauth_verifier')
			)
		);
		// check response_code ? 200 OK
		if ( $response_code == 200 )
		{	
			//get parameters from response
			$twitter_access_token = $this->extract_params($this->response['response']);
			//write granted access tokens to session
			\Session::set("twitter_access_token", $twitter_access_token);
			//delete temproraly oauth tokens from session
			\Session::delete("twitter_oauth");
			//refresh the page
			\Response::redirect(\Uri::current());
		} 
		// if response_code not 200
		else
		{
			return false;
		}
	}
}


class Twitter{

	/**
	 * Singleton constructions
	 */
	private function __construct() { 
	}

	/**
	 * @var  string  $version  The current version of the package
	 */
	public static $version = '1.0';

	/**
	 * @var  tmhOAuth  $tmhoauth  Holds the tmhOAuth instance.
	 */
	public static $tmhoauth = null;
	
	/**
	 * Creates the tmhOAuth instance
	 *
	 * @return  void
	 */
	public static function _init()
	{
		static::$tmhoauth = new TwitterOAuth();
	}

	/**
	 * Pass-through to the tmhOAuthExtended instance.
	 *
	 * @param   string  $method  The called method
	 * @param   array   $args    The method arguments
	 * @return  mixed   The method results
	 * @throws  BadMethodCallException
	 */
	public static function __callStatic($method, $args)
	{
		if (is_callable(array(static::$tmhoauth, $method)))
		{
			return call_user_func_array(array(static::$tmhoauth, $method), $args);
		}
		throw new \BadMethodCallException("Method Twitter::$method does not exist.");
	}
}