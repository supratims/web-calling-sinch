<?php
require 'vendor/autoload.php';
require_once 'init.php';
require 'Auth.php';
require 'SinchTicketGenerator.php';
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

$username = $_POST['username'];
$password = $_POST['password'];
$route = $_POST['type'];
if (empty($route)){
	$route = $_GET['type'];
}

function redirect($url){
	header("Location: $url"); 
	die();
}

function saveToken($username, $signedUserTicket){
	$query = new ParseQuery("SinchAuth");
	$query->equalTo("username", $username);
	$results = $query->find();
	if (count($results)>0){
		$results[0]->destroy();
		$results[0]->save();
	}
	else {
		$sinchauth = new ParseObject("SinchAuth");
		$sinchauth->set("username", $username);
		$sinchauth->set("token", $signedUserTicket);
		try {
			$sinchauth->save();
		} catch (ParseException $ex) {  
		  
		}
	}
}

function login($username, $password){
	global $keys;

	try {
		$auth = new Auth();
		$auth->login($username, $password);
		$generator = new SinchTicketGenerator($keys['sinch-app-key'], $keys['sinch-secret-key']);
		$signedUserTicket = $generator->generateTicket($username, new DateTime(), 3600);
		$user = ParseUser::getCurrentUser();
		saveToken($username, $signedUserTicket);
		redirect('/?token='.$user->getSessionToken());
	}
	catch(Exception $e){
		//echo 'error,'.$e->getMessage();
		redirect('/?error='.$e->getMessage());
	}
}

function register($username, $password){
	try {
		$auth = new Auth();
		$auth->registerUser($username, $password);
	}
	catch(Exception $e){
		//echo 'error,'.$e->getMessage();
		redirect('/?error='.$e->getMessage());
	}
}

if ($route=='login'){
	login($username, $password);
}

if ($route=='register'){
	register($username, $password);
	login($username, $password);
}

echo 'Oops. Something went wrong.';