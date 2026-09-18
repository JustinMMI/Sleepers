<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$idGenr = api_int_input('idGenr');
$nomEUser = api_input('nomEUser');
$prenomUser = api_input('prenomUser');
$emailUser = api_input('emailUser');
$passwordUser = api_input('passwordUser');

if (!filter_var($emailUser, FILTER_VALIDATE_EMAIL)) {
	http_response_code(400);
	exit('Adresse email invalide');
}

if (strlen($passwordUser) < 8) {
	http_response_code(400);
	exit('Le mot de passe doit contenir au moins 8 caracteres');
}

$existing = api_execute(
	'SELECT idUser FROM `USER` WHERE emailUser = :emailUser',
	array(':emailUser' => $emailUser)
)->fetch();

if ($existing) {
	http_response_code(409);
	exit('Cette adresse email est deja utilisee');
}

api_execute(
	'INSERT INTO `USER` (idGenr, nomEUser, prenomUser, emailUser, passwordUser) '
	. 'VALUES (:idGenr, :nomEUser, :prenomUser, :emailUser, :passwordUser)',
	array(
		':idGenr' => $idGenr,
		':nomEUser' => $nomEUser,
		':prenomUser' => $prenomUser,
		':emailUser' => $emailUser,
		':passwordUser' => password_hash($passwordUser, PASSWORD_DEFAULT),
	)
);

api_redirect('/views/backend/security/login.php');

