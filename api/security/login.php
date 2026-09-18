<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$emailUser = api_input('emailUser');
$passwordUser = api_input('passwordUser');

$user = api_execute(
	'SELECT idUser, passwordUser FROM `USER` WHERE emailUser = :emailUser',
	array(':emailUser' => $emailUser)
)->fetch();

if (!$user || !password_verify($passwordUser, $user['passwordUser'])) {
	http_response_code(401);
	exit('Identifiants invalides');
}

session_regenerate_id(true);
$_SESSION['id_user'] = (int) $user['idUser'];
$_SESSION['USER_ID'] = (int) $user['idUser'];

api_redirect('/');

