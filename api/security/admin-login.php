<?php

require_once dirname(__DIR__) . '/bootstrap.php';

$password = api_input('adminPassword');
$hash = getenv('ADMIN_PASSWORD_HASH');

if (!$hash || !password_verify($password, $hash)) {
    header('Location: ' . ROOT_URL . '/views/backend/security/admin-login.php?error=1');
    exit;
}

session_regenerate_id(true);
$_SESSION['ADMIN_ACCESS'] = true;

api_redirect('/views/backend/dashboard.php');
