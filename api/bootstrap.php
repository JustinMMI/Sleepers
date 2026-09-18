<?php

require_once dirname(__DIR__) . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function api_db()
{
    static $db;

    if (!$db) {
        $db = new PDO(
            'mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB . ';charset=utf8mb4',
            SQL_USER,
            SQL_PWD,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            )
        );
    }

    return $db;
}

function api_input($name, $required = true)
{
    $value = isset($_POST[$name]) ? trim((string) $_POST[$name]) : '';

    if ($required && $value === '') {
        http_response_code(400);
        exit('Parametre manquant : ' . $name);
    }

    return $value;
}

function api_int_input($name, $required = true)
{
    $value = api_input($name, $required);

    if ($value === '' && !$required) {
        return null;
    }

    if (filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value < 1) {
        http_response_code(400);
        exit('Parametre invalide : ' . $name);
    }

    return (int) $value;
}

function api_redirect($path)
{
    header('Location: ' . ROOT_URL . $path);
    exit;
}

function api_execute($sql, $parameters = array())
{
    $statement = api_db()->prepare($sql);
    $statement->execute($parameters);

    return $statement;
}
