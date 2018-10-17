<?php

require('vendor/autoload.php');

use PressLib\Client;

$press = new Client('http://www.statelyvelvet.com');

echo $press->ping ."\n";
echo $press->pages->getEndpoint() ."\n";
echo $press->posts->getEndpoint() . "\n";

echo print_r($press->posts->bySlug('starring-in-my-own-story'), true) . "\n";

// print_r($press->posts->all());
// print_r($press->pages->all());
