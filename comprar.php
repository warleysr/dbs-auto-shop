<?php
require('PagSeguroLibrary/PagSeguroLibrary.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $codigo = trim(addslashes($_POST["produto"]));
      if (!(is_numeric($codigo))) {
        header("location: ./");
        return;
      }

      $config = include('config.php');

      $con = mysqli_connect($config["host"], $config["user"], $config["pass"], $config["db"]) 
      or die(mysqli_connect_error());
      $query = mysqli_query($con, "SELECT * FROM `produtos` WHERE `codigo` = '$codigo'") or die(mysqli_error($con));
      $count = mysqli_num_rows($query);
      if ($count != 1) {
        mysqli_close($con);
        header("location: ./");
        return;
      }

      $produto = $query->fetch_array(MYSQLI_ASSOC);
      $nome = $produto["nome"];
      $preco = $produto["preco"];

      mysqli_close($con);

	    $nick = base64_decode($_POST["player"]);
      $referencia = $nick . ":" . $codigo;

	    // Iniciando solicitação de pagamento
	    $paymentRequest = new PagSeguroPaymentRequest();  

	    // Adicionando um item ao carrinho
    	$paymentRequest->addItem($codigo, $nome . " para " . $nick, 1, $preco);

        // Definir moeda do pagamento
    	$paymentRequest->setCurrency("BRL"); 

    	// Referenciando a transação do PagSeguro em seu sistema  
    	$paymentRequest->setReference($referencia);  
      
    	// URL para onde o comprador será redirecionado (GET) após o fluxo de pagamento  
    	$paymentRequest->setRedirectUrl($config["redirectURL"]);  
      
    	// URL para onde serão enviadas notificações (POST) indicando alterações no status da transação  
    	$paymentRequest->addParameter('notificationURL', $config["notificationURL"]);  

    	try {  
          $mode = $config["sandbox"] ? "sandbox" : "production";
      		$credentials = new PagSeguroAccountCredentials($config["email"], $config["token"]);
      		$checkoutUrl = $paymentRequest->register($credentials);

      		// Redirecionar usuário para o pagamento
      		header("location: $checkoutUrl");
      
    	} catch (PagSeguroServiceException $e) {  
        	die($e->getMessage());  
    	}  
} else {
	header("location: ./");
}
?>