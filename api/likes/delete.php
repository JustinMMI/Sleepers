<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$idUserL1 = api_int_input('idUserL1');
$idUserL2 = api_int_input('idUserL2');

api_execute(
	'DELETE FROM LIKES WHERE idUserL1 = :idUserL1 AND idUserL2 = :idUserL2',
	array(':idUserL1' => $idUserL1, ':idUserL2' => $idUserL2)
);

api_redirect('/views/backend/likes/list.php');

