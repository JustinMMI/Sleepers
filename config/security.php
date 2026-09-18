<?php
/**
 * Check if user is logged in with SESSION
 */

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (isset($_SESSION['USER_ID']) && (int) $_SESSION['USER_ID'] > 0 && !defined('ID_USER')) {
	define('ID_USER', (int) $_SESSION['USER_ID']);
}

function is_admin_authenticated()
{
	return !empty($_SESSION['ADMIN_ACCESS']);
}

function require_admin_access()
{
	$script = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';

	$is_backend_page = strpos($script, '/views/backend/') !== false && strpos($script, '/views/backend/security/') === false;
	$is_admin_api = strpos($script, '/api/') !== false && strpos($script, '/api/security/') === false;

	if (($is_backend_page || $is_admin_api) && !is_admin_authenticated()) {
		if ($is_admin_api) {
			http_response_code(403);
			exit('Acces administrateur requis');
		}

		header('Location: ' . ROOT_URL . '/views/backend/security/admin-login.php');
		exit;
	}
}

require_admin_access();

?>