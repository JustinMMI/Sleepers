<?php

require_once dirname(__DIR__) . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if (!defined('ID_USER')) {
    http_response_code(401);
    echo json_encode(array('success' => false, 'error' => 'Connexion requise'));
    exit;
}

$targetId = api_int_input('targetId');
$action = api_input('action'); // 'like', 'pass', 'super'

if ($targetId === ID_USER) {
    http_response_code(400);
    echo json_encode(array('success' => false, 'error' => 'Action impossible sur soi-meme'));
    exit;
}

$likeValue = ($action === 'like' || $action === 'super') ? 1 : 0;

// Vérifier si l'utilisateur cible existe réellement en base
$target = api_execute(
    'SELECT idUser, nomEUser, prenomUser, photo, age, biographie FROM `USER` WHERE idUser = :id LIMIT 1',
    array(':id' => $targetId)
)->fetch();

$isMatch = false;
$partnerInfo = null;

if (!$target) {
    http_response_code(404);
    echo json_encode(array('success' => false, 'error' => 'Utilisateur introuvable dans la table USER'));
    exit;
}

// Enregistrement strict dans la table LIKES
$existingLike = api_execute(
    'SELECT likeL1 FROM LIKES WHERE idUserL1 = :l1 AND idUserL2 = :l2',
    array(':l1' => ID_USER, ':l2' => $targetId)
)->fetch();

if ($existingLike !== false) {
    api_execute(
        'UPDATE LIKES SET likeL1 = :likeVal WHERE idUserL1 = :l1 AND idUserL2 = :l2',
        array(':likeVal' => $likeValue, ':l1' => ID_USER, ':l2' => $targetId)
    );
} else {
    api_execute(
        'INSERT INTO LIKES (idUserL1, idUserL2, likeL1) VALUES (:l1, :l2, :likeVal)',
        array(':l1' => ID_USER, ':l2' => $targetId, ':likeVal' => $likeValue)
    );
}

if ($likeValue === 1) {
    // Vérifier si le partenaire nous a déjà liké dans LIKES
    $reciprocal = api_execute(
        'SELECT 1 FROM LIKES WHERE idUserL1 = :targetId AND idUserL2 = :myId AND likeL1 = 1 LIMIT 1',
        array(':targetId' => $targetId, ':myId' => ID_USER)
    )->fetchColumn();

    if ($reciprocal !== false || $action === 'super') {
        $isMatch = true;

        $matchExists = api_execute(
            'SELECT 1 FROM MATCHS WHERE (idUserM1 = :m1 AND idUserM2 = :m2) OR (idUserM1 = :m3 AND idUserM2 = :m4) LIMIT 1',
            array(':m1' => ID_USER, ':m2' => $targetId, ':m3' => $targetId, ':m4' => ID_USER)
        )->fetchColumn();

        if ($matchExists === false) {
            api_execute(
                'INSERT INTO MATCHS (idUserM1, idUserM2) VALUES (:insM1, :insM2)',
                array(':insM1' => ID_USER, ':insM2' => $targetId)
            );
        }

        $partnerInfo = array(
            'id' => (int) $target['idUser'],
            'name' => trim($target['prenomUser'] . ' ' . $target['nomEUser']),
            'photo' => $target['photo'],
            'age' => $target['age'] !== null ? (int) $target['age'] : null,
            'bio' => $target['biographie']
        );
    }
}

echo json_encode(array(
    'success' => true,
    'action' => $action,
    'is_match' => $isMatch,
    'partner' => $partnerInfo
));
