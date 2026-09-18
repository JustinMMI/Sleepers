<?php

require_once dirname(__DIR__) . '/bootstrap.php';

unset($_SESSION['ADMIN_ACCESS']);

api_redirect('/');
