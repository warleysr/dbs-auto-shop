<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$config = include('config.php');

	$email = $config["email"];
	$token = $config["token"];
	$sandbox = $config["sandbox"];
	$code = $_POST["notificationCode"];

    $mode = $sandbox ? "sandbox." : "";
	$url = "https://ws." . $mode . "pagseguro.uol.com.br/v3/transactions/notifications/$code?email=$email&token=$token";

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$transactionCurl = curl_exec($curl);
	curl_close($curl);

	$transaction = simplexml_load_string($transactionCurl);
	
	$status = $transaction->status;
	$codigo = $transaction->code;
	$referencia = $transaction->reference;

	$ref = explode(":", $referencia);
	$nick = $ref[0];
	$produto = $ref[1];

    $con = mysqli_connect($config["host"], $config["user"], $config["pass"], $config["db"]) 
    or die (mysqli_connect_error());
	if ($status == 3) {
		mysqli_query($con, "INSERT INTO `pendentes` (`nick`, `produto`, `codigo`) 
			VALUES ('$nick', '$produto', '$codigo')") or die(mysqli_error($con));

	} else if (($status == 5) || ($status == 6) || ($status == 8)) {
		mysqli_query($con, "DELETE FROM `pendentes` WHERE `codigo` = '$codigo'") or die(mysqli_error($con));

		mysqli_query($con, "INSERT INTO `estornados` (`nick`, `produto`, `codigo`, `status`) 
			VALUES ('$nick', '$produto', '$codigo', '$status') ON DUPLICATE KEY UPDATE `status` = '$status'")
			or die(mysqli_error($con));
	}
	
	mysqli_close($con);

} else {
	header("location: ./");
}
?>
