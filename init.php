<?php
require 'vendor/autoload.php';
require_once 'appkeys.php';

use Parse\ParseClient;

/*
function loadKeys(){
	$keys_json = file_get_contents(dirname(__FILE__).'/appkeys.json');
	$keys = json_decode($keys_json, true);
	return $keys;
}

$keys = loadKeys();
*/
global $keys;

ParseClient::initialize( $keys['parse-app-id'], $keys['parse-rest-key'], $keys['parse-master-key'] );
