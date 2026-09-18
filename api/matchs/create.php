<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$idUserM1 = api_int_input('idUserM1');
$idUserM2 = api_int_input('idUserM2');

api_execute(
	'INSERT INTO MATCHS (idUserM1, idUserM2) VALUES (:idUserM1, :idUserM2)',
	array(':idUserM1' => $idUserM1, ':idUserM2' => $idUserM2)
);

api_redirect('/views/backend/matchs/list.php');

