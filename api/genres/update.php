<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$idGenr = api_int_input('idGenr');
$libGenr = api_input('libGenr');

api_execute(
	'UPDATE GENRE SET libGenr = :libGenr WHERE idGenr = :idGenr',
	array(':libGenr' => $libGenr, ':idGenr' => $idGenr)
);

api_redirect('/views/backend/genres/list.php');

