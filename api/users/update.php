<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$idUser = api_int_input('idUser');
$fields = array(
	'idGenr' => api_int_input('idGenr', false),
	'nomEUser' => api_input('nomEUser', false),
	'prenomUser' => api_input('prenomUser', false),
	'emailUser' => api_input('emailUser', false),
	'passwordUser' => api_input('passwordUser', false),
	'photo' => api_input('photo', false),
	'age' => api_int_input('age', false),
	'biographie' => api_input('biographie', false),
);

if ($fields['emailUser'] !== '' && !filter_var($fields['emailUser'], FILTER_VALIDATE_EMAIL)) {
	http_response_code(400);
	exit('Adresse email invalide');
}

if ($fields['emailUser'] !== '' && api_execute(
	'SELECT 1 FROM `USER` WHERE emailUser = :emailUser AND idUser <> :idUser LIMIT 1',
	array(':emailUser' => $fields['emailUser'], ':idUser' => $idUser)
)->fetchColumn() !== false) {
	http_response_code(409);
	exit('Cette adresse email est deja utilisee');
}

$set = array();
$parameters = array(':idUser' => $idUser);
foreach ($fields as $field => $value) {
	if ($value === '' || $value === null) {
		continue;
	}
	if ($field === 'passwordUser') {
		$value = password_hash($value, PASSWORD_DEFAULT);
	}
	$parameter = ':' . $field;
	$set[] = $field . ' = ' . $parameter;
	$parameters[$parameter] = $value;
}

if (!$set) {
	http_response_code(400);
	exit('Aucune modification fournie');
}

api_execute('UPDATE `USER` SET ' . implode(', ', $set) . ' WHERE idUser = :idUser', $parameters);

api_redirect('/views/backend/users/list.php');

