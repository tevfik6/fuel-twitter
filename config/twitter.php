<?php
return array(
	'active' => Fuel::$env,

    // general configurations
	'default' => array(
        // default timezone for requests
        'timezone'                   => \Config::get("default_timezone"),

        //NOTE: some configuration sets (guess, not necessary for web apps, later should remove these lines)
        // you can get the latest cacert.pem from here http://curl.haxx.se/ca/cacert.pem
        'curl_cainfo' => \Package::exists('twitter').'classes/tmhOAuth/cacert.pem',
        'curl_capath' => \Package::exists('twitter').'classes/tmhOAuth',
    ),
);
