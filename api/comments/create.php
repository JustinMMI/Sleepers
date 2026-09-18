<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$idUserC1 = api_int_input('idUserC1');
$idUserC2 = api_int_input('idUserC2');
$libComment = api_input('libComment');

api_execute(
	'INSERT INTO COMMENTS (idUserC1, idUserC2, libComment) VALUES (:idUserC1, :idUserC2, :libComment)',
	array(':idUserC1' => $idUserC1, ':idUserC2' => $idUserC2, ':libComment' => $libComment)
);

api_redirect('/views/backend/comments/list.php');

