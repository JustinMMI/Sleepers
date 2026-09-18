<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$userId = isset($_POST['userId']) ? (int) $_POST['userId'] : 0;

if ($userId > 0) {
	$user = api_execute(
		'SELECT idUser, prenomUser, nomEUser FROM `USER` WHERE idUser = :id',
		array(':id' => $userId)
	)->fetch();

	if ($user) {
		session_regenerate_id(true);
		$_SESSION['id_user'] = (int) $user['idUser'];
		$_SESSION['USER_ID'] = (int) $user['idUser'];
	}
}

api_redirect('/');

