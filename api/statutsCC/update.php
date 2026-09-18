<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$numStat = api_int_input('numStat');
$libStat = api_input('libStat');

api_execute(
	'UPDATE STATUT SET libStat = :libStat WHERE numStat = :numStat',
	array(':libStat' => $libStat, ':numStat' => $numStat)
);

api_redirect('/views/backend/statutsCC/list.php');

