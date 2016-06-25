<?php 

require("AuthMe.class.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$user = trim(addslashes($_POST["player"]));
	$pass = trim(addslashes($_POST["password"]));

	$config = include('config.php');

	$authme = new AuthMe($config["authme.host"], $config["authme.user"], $config["authme.pass"], $config["authme.db"], $config["authme.table"], $config["authme.hash"]);
	if ($authme->authenticate($user, $pass)) {
		echo "true";
	} else {
		echo "false";
	}
} else {
	header("location: ./");
}
?>