<?php 
return array(
	# Nome do seu server (aparecerá no titulo das páginas)
	'server-name' => 'SeuServer',
	# PagSeguro
	'email' => 'SEU-EMAIL',
	'token' => 'SEU-TOKEN',
	# Lembre-se de alterar o token ao ativar/desativar o modo SandBox
	# O token do modo SandBox é diferente do modo de Produção
	'sandbox' => false,
	# URL para redirecionar após a compra
	'redirectURL' => 'http://seusite.com.br/vip/obrigado.php',
	# URL para retornar notificações automáticas
	'notificationURL' => 'http://seusite.com.br/vip/retorno.php',
	# Banco de Dados
	'host' => 'localhost',
	'user' => 'root',
	'pass' => '',
	'db' => 'test',
	# AuthMe
	'authme.host' => 'localhost',
	'authme.user' => 'root',
	'authme.pass' => '',
	'authme.db' => 'test',
	'authme.table' => 'authme',
	'authme.hash' => 'sha256'
	);
?>