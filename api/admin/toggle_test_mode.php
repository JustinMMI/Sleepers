<?php

require_once dirname(__DIR__) . '/bootstrap.php';

// Protection : accès réservé à l'administrateur
if (!function_exists('is_admin_authenticated') || !is_admin_authenticated()) {
    http_response_code(403);
    exit('Accès réservé aux administrateurs.');
}

$settings = load_app_settings();
$newState = !empty($_POST['enable']) ? true : false;
$settings['test_bdd_enabled'] = $newState;

save_app_settings($settings);

header('Location: ' . ROOT_URL . '/views/backend/dashboard.php?saved=1');
exit;

