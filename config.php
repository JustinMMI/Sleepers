<?php
// Define the application root independently from the web server document root.
define('ROOT', __DIR__);

$document_root = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
$application_root = realpath(__DIR__);
$base_path = '';
if ($document_root && $application_root && strpos($application_root, $document_root) === 0) {
    $base_path = str_replace('\\', '/', substr($application_root, strlen($document_root)));
}
define('ROOT_URL', 'http://' . $_SERVER['HTTP_HOST'] . $base_path);

//Load env
require_once ROOT . '/includes/libs/DotEnv.php';
(new DotEnv(ROOT.'/.env'))->load();

//defines
require_once ROOT . '/config/defines.php';

//debug
if (getenv('APP_DEBUG') == 'true') {
    require_once ROOT . '/config/debug.php';
}

//load functions
require_once ROOT . '/functions/global.inc.php';

//load security
require_once ROOT . '/config/security.php';