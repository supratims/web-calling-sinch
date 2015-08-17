<?php

require 'vendor/autoload.php';
require 'Auth.php';

$auth = new Auth();

try {
	$auth->logout();
}
catch(Exception $e){
	echo 'error,'.$e->getMessage();
}
