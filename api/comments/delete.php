<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$idUserC1 = api_int_input('idUserC1');
$idUserC2 = api_int_input('idUserC2');

api_execute(
	'DELETE FROM COMMENTS WHERE idUserC1 = :idUserC1 AND idUserC2 = :idUserC2',
	array(':idUserC1' => $idUserC1, ':idUserC2' => $idUserC2)
);

api_redirect('/views/backend/comments/list.php');

