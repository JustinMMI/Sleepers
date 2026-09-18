<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$oldIdUserM1 = api_int_input('oldIdUserM1', false);
$oldIdUserM2 = api_int_input('oldIdUserM2', false);
$idUserM1 = api_int_input('idUserM1');
$idUserM2 = api_int_input('idUserM2');
api_require_distinct_pair($idUserM1, $idUserM2);

if ($oldIdUserM1 === null) {
	$oldIdUserM1 = $idUserM1;
}
if ($oldIdUserM2 === null) {
	$oldIdUserM2 = $idUserM2;
}

if (($idUserM1 !== $oldIdUserM1 || $idUserM2 !== $oldIdUserM2)
	&& api_relation_exists('MATCHS', 'idUserM1', $idUserM1, 'idUserM2', $idUserM2)) {
	http_response_code(409);
	exit('Ce match existe deja');
}

api_execute(
	'UPDATE MATCHS SET idUserM1 = :idUserM1, idUserM2 = :idUserM2 '
	. 'WHERE idUserM1 = :oldIdUserM1 AND idUserM2 = :oldIdUserM2',
	array(
		':idUserM1' => $idUserM1,
		':idUserM2' => $idUserM2,
		':oldIdUserM1' => $oldIdUserM1,
		':oldIdUserM2' => $oldIdUserM2,
	)
);

api_redirect('/views/backend/matchs/list.php');

