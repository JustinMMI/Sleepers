<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$libGenr = api_input('libGenr');

api_execute('INSERT INTO GENRE (libGenr) VALUES (:libGenr)', array(
	':libGenr' => $libGenr,
));

api_redirect('/views/backend/genres/list.php');

