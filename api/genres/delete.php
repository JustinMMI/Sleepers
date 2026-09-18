<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$idGenr = api_int_input('idGenr');

api_execute('DELETE FROM GENRE WHERE idGenr = :idGenr', array(':idGenr' => $idGenr));

api_redirect('/views/backend/genres/list.php');

