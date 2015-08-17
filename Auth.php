<?php
require 'vendor/autoload.php';
require_once 'init.php';

use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseACL;
use Parse\ParsePush;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParseException;
use Parse\ParseAnalytics;
use Parse\ParseFile;
use Parse\ParseCloud;
use Parse\ParseClient;

class Auth {
	function registerUser($username, $password){
		$user = new ParseUser();
		$user->setUsername($username);
		$user->setPassword($password);
		try {
		    $user->signUp();
		} catch (ParseException $ex) {
		    throw $ex;
		}
	}

	function login($username, $password){
		try {
		    $user = ParseUser::logIn($username, $password);
		} catch(ParseException $ex) {
		    throw $ex;
		}
	}

	function logout(){
		ParseUser::logOut();
	}
}


