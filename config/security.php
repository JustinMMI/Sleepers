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

?>