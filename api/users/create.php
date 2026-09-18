<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$idGenr = api_int_input('idGenr');
$nomEUser = api_input('nomEUser', false);
$prenomUser = api_input('prenomUser', false);
$emailUser = api_input('emailUser', false);
$passwordUser = api_input('passwordUser', false);
$photo = api_input('photo', false);
$age = api_int_input('age', false);
$biographie = api_input('biographie', false);

if ($emailUser !== '' && !filter_var($emailUser, FILTER_VALIDATE_EMAIL)) {
	http_response_code(400);
	exit('Adresse email invalide');
}

if ($emailUser !== '' && api_execute(
	'SELECT 1 FROM `USER` WHERE emailUser = :emailUser LIMIT 1',
	array(':emailUser' => $emailUser)
)->fetchColumn() !== false) {
	http_response_code(409);
	exit('Cette adresse email est deja utilisee');
}

if ($passwordUser !== '') {
	$passwordUser = password_hash($passwordUser, PASSWORD_DEFAULT);
}

api_execute(
	'INSERT INTO `USER` '
	. '(idGenr, nomEUser, prenomUser, emailUser, passwordUser, photo, age, biographie) '
	. 'VALUES (:idGenr, :nomEUser, :prenomUser, :emailUser, :passwordUser, :photo, :age, :biographie)',
	array(
		':idGenr' => $idGenr,
		':nomEUser' => $nomEUser ?: null,
		':prenomUser' => $prenomUser ?: null,
		':emailUser' => $emailUser ?: null,
		':passwordUser' => $passwordUser ?: null,
		':photo' => $photo ?: null,
		':age' => $age,
		':biographie' => $biographie ?: null,
	)
);

api_redirect('/views/backend/users/list.php');

