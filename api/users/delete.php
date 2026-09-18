<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$idUser = api_int_input('idUser');

api_execute('DELETE FROM `USER` WHERE idUser = :idUser', array(':idUser' => $idUser));

api_redirect('/views/backend/users/list.php');

