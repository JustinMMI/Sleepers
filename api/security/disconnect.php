<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$_SESSION = array();

if (ini_get('session.use_cookies')) {
	$parameters = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000, $parameters['path'], $parameters['domain'], $parameters['secure'], $parameters['httponly']);
}

session_destroy();

api_redirect('/');

