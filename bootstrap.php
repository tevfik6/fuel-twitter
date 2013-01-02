<?php

Autoloader::add_core_namespace('Twitter');

Autoloader::add_classes(array(
	'Twitter\\Twitter'          => __DIR__.'/classes/twitter.php',
	'Twitter\\TwitterException' => __DIR__.'/classes/twitter.php',
	
	'tmhOAuth'                  => __DIR__.'/classes/tmhOAuth/tmhOAuth.php',
));
