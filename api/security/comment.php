<?php

require_once dirname(__DIR__) . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if (!defined('ID_USER')) {
    http_response_code(401);
    echo json_encode(array('success' => false, 'error' => 'Connexion requise'));
    exit;
}

$targetId = api_int_input('targetId');
$message = api_input('message');

if ($targetId === ID_USER) {
    http_response_code(400);
    echo json_encode(array('success' => false, 'error' => 'Action impossible sur soi-meme'));
    exit;
}

if (mb_strlen($message) > 300) {
    $message = mb_substr($message, 0, 300);
}

// Vérifier si l'utilisateur cible existe réellement en base
$targetExists = api_execute(
    'SELECT 1 FROM `USER` WHERE idUser = :id LIMIT 1',
    array(':id' => $targetId)
)->fetchColumn();

if ($targetExists === false) {
    http_response_code(404);
    echo json_encode(array('success' => false, 'error' => 'Utilisateur introuvable dans la table USER'));
    exit;
}

$existingComment = api_execute(
    'SELECT 1 FROM COMMENTS WHERE idUserC1 = :c1 AND idUserC2 = :c2',
    array(':c1' => ID_USER, ':c2' => $targetId)
)->fetchColumn();

if ($existingComment !== false) {
    api_execute(
        'UPDATE COMMENTS SET libComment = :msg WHERE idUserC1 = :c1 AND idUserC2 = :c2',
        array(':msg' => $message, ':c1' => ID_USER, ':c2' => $targetId)
    );
} else {
    api_execute(
        'INSERT INTO COMMENTS (idUserC1, idUserC2, libComment) VALUES (:c1, :c2, :msg)',
        array(':c1' => ID_USER, ':c2' => $targetId, ':msg' => $message)
    );
}

echo json_encode(array('success' => true, 'message' => $message));
