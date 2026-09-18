<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$idUserL1 = api_int_input('idUserL1');
$idUserL2 = api_int_input('idUserL2');
$likeL1 = api_input('likeL1');

if (!in_array($likeL1, array('0', '1'), true)) {
	http_response_code(400);
	exit('Parametre invalide : likeL1');
}

api_execute(
	'UPDATE LIKES SET likeL1 = :likeL1 WHERE idUserL1 = :idUserL1 AND idUserL2 = :idUserL2',
	array(':likeL1' => (int) $likeL1, ':idUserL1' => $idUserL1, ':idUserL2' => $idUserL2)
);

api_redirect('/views/backend/likes/list.php');

