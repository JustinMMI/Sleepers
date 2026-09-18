<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$idUserC1 = api_int_input('idUserC1');
$idUserC2 = api_int_input('idUserC2');
$libComment = api_input('libComment');

api_execute(
	'UPDATE COMMENTS SET libComment = :libComment '
	. 'WHERE idUserC1 = :idUserC1 AND idUserC2 = :idUserC2',
	array(':libComment' => $libComment, ':idUserC1' => $idUserC1, ':idUserC2' => $idUserC2)
);

api_redirect('/views/backend/comments/list.php');

