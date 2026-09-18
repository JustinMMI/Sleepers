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

function api_require_distinct_pair($first, $second)
{
    if ($first === $second) {
        http_response_code(400);
        exit('Une relation doit concerner deux utilisateurs differents');
    }
}

function api_relation_exists($table, $firstField, $first, $secondField, $second)
{
    $allowedTables = array('LIKES', 'MATCHS', 'COMMENTS');
    $allowedFields = array('idUserL1', 'idUserL2', 'idUserM1', 'idUserM2', 'idUserC1', 'idUserC2');

    if (!in_array($table, $allowedTables, true) || !in_array($firstField, $allowedFields, true) || !in_array($secondField, $allowedFields, true)) {
        http_response_code(500);
        exit('Relation invalide');
    }

    return api_execute(
        'SELECT 1 FROM ' . $table . ' WHERE ' . $firstField . ' = :first AND ' . $secondField . ' = :second LIMIT 1',
        array(':first' => $first, ':second' => $second)
    )->fetchColumn() !== false;
}

function api_json($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function api_error($statusCode, $message)
{
    api_json(array('success' => false, 'error' => $message), $statusCode);
}
