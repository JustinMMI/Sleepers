<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$numStat = api_int_input('numStat');

api_execute('DELETE FROM STATUT WHERE numStat = :numStat', array(':numStat' => $numStat));

api_redirect('/views/backend/statutsCC/list.php');