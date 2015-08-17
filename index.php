<?php
require 'vendor/autoload.php';
require_once 'init.php';
require 'SinchTicketGenerator.php';
require 'Auth.php';
use Parse\ParseUser;
use Parse\ParseQuery;

$token = $_GET['token'];
if (!empty($token)){
	try {
		$user = ParseUser::become($token);
		// The current user is now set to user.
	} catch (ParseException $ex) {
		// The token could not be validated.
	}
}

//$user = ParseUser::getCurrentUser();
//echo '<'.print_r($user, true).'>';

$error = $_GET['error'];
if (!empty($user)){
	$query = new ParseQuery("SinchAuth");
	//echo '{'.$user->getUsername().'}';
	$query->equalTo("username", $user->getUsername());
	$results = $query->find();
	if (count($results)>0){
		$token = $results[0]->get('token');
		//echo '['.$token.']';
	}
	//$generator = new SinchTicketGenerator('d3149e64-2f1b-41d7-a9f6-c507ca26fbc4', '+h0Zby8fsUeMTEyGVtrUAQ==');
	//$signedUserTicket = $generator->generateTicket($username, new DateTime(), 3600);
	$signedUserTicket = $token;
}
global $keys;

$sinch_app_key = $keys['sinch-app-key'];
?>
<html>
<head>
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	<title>Web Caller</title>
	<link rel="stylesheet" href="style/style.css"/>
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:300' rel='stylesheet' type='text/css'>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="lib/sinch.min.js"></script>
</head>

<body>
	<div class="container">
		<div class="top1">
			<h1>Web Calling</h1>
		</div>
		<div id="chromeFileWarning" class="frame big">
			<b style="color: red;">Warning!</b> Protocol "file" used to load page in Chrome.<br><br>
			Please avoid loading files directly from disk when developing WebRTC applications using Chrome.<br>
			Chrome disables access to microphone which prevents proper functionality.<br>
			<br>
			You can allow working with "file:", if you start Chrome with the flag <i>--allow-file-access-from-files</i>
		</div>

		<div class="clearfix"></div>
		<div class="error">
			<?php
			if (!empty($error)){
				echo $error;
			} ?>
		</div>
		<div class="frame small">
			<div class="inner loginBox">
			<?php
			if (empty($user)){
			?>
				<div id="loginform">
				<form id="signin" method="post">
					<h3 id="loginarea">Sign in</h3>
					<input id="username" name="username" placeholder="EMAIL"><br>
					<input id="password" name="password" type="password" placeholder="PASSWORD"><br>
				</form>
					<button id="login">Login</button>
					<button id="register">Sign Up</button>
				</div>
			<?php
			}
			else {
			?>
				<div id="userInfo">
					<h3>
					<span id="username">
						<?php 
							echo $user->getUsername();
						?>
					</span>
					</h3>
					<button id="logout">Logout</button>
				</div>
			<?php
			}
			?>
			</div>
		</div>

		<div class="frame">
			<input type="hidden" value="<?php echo $sinch_app_key; ?>" id="sinchApplicationKey">
			<?php
			if (!empty($user)){
			?>
				<h3>Web Call</h3>
				<div id="call">
					<input type="hidden" value="<?php echo $signedUserTicket; ?>" id="userTicket">
					<input type="hidden" value="<?php echo $user->getUsername(); ?>" id="username_auth">
					<form id="newCall">
						<input id="phoneNumber" placeholder="Phone Number (+46000000000)"><br>
						<button id="call">Call</button>
						<button id="hangup">Hangup</button>
						<audio id="incoming" autoplay></audio>
						<audio id="ringback" src='style/ringback.wav' loop></audio>
					</form>
				</div>
				<div class="clearfix"></div>
				<div id="callLog">
				</div>
				<div class="error">
				</div>
			<?php
			}
			?>
		</div>
	</div>
</body>

<script src="js/webcaller.js"></script>

</html>