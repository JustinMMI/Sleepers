<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$idUserL1 = api_int_input('idUserL1');
$idUserL2 = api_int_input('idUserL2');
$likeL1 = api_input('likeL1');
api_require_distinct_pair($idUserL1, $idUserL2);

if (!in_array($likeL1, array('0', '1'), true)) {
	http_response_code(400);
	exit('Parametre invalide : likeL1');
}

if (api_relation_exists('LIKES', 'idUserL1', $idUserL1, 'idUserL2', $idUserL2)) {
	http_response_code(409);
	exit('Cette relation existe deja');
}

api_execute(
	'INSERT INTO LIKES (idUserL1, idUserL2, likeL1) VALUES (:idUserL1, :idUserL2, :likeL1)',
	array(':idUserL1' => $idUserL1, ':idUserL2' => $idUserL2, ':likeL1' => (int) $likeL1)
);

api_redirect('/views/backend/likes/list.php');

