<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$libStat = api_input('libStat');

api_execute('INSERT INTO STATUT (libStat) VALUES (:libStat)', array(':libStat' => $libStat));

api_redirect('/views/backend/statutsCC/list.php');