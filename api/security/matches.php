<?php

require_once dirname(__DIR__) . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if (!defined('ID_USER')) {
    http_response_code(401);
    echo json_encode(array('success' => false, 'error' => 'Connexion requise'));
    exit;
}

$matches = api_execute(
    'SELECT 
        u.idUser, 
        u.prenomUser, 
        u.nomEUser, 
        u.photo, 
        u.age, 
        u.biographie,
        g.libGenr,
        (SELECT c.libComment FROM COMMENTS c 
         WHERE (c.idUserC1 = :myId1 AND c.idUserC2 = u.idUser) 
            OR (c.idUserC1 = u.idUser AND c.idUserC2 = :myId2) 
         LIMIT 1) AS lastComment
     FROM MATCHS m
     JOIN `USER` u ON (u.idUser = CASE WHEN m.idUserM1 = :myId3 THEN m.idUserM2 ELSE m.idUserM1 END)
     LEFT JOIN GENRE g ON (g.idGenr = u.idGenr)
     WHERE m.idUserM1 = :myId4 OR m.idUserM2 = :myId5
     ORDER BY u.idUser DESC',
    array(
        ':myId1' => ID_USER,
        ':myId2' => ID_USER,
        ':myId3' => ID_USER,
        ':myId4' => ID_USER,
        ':myId5' => ID_USER,
    )
)->fetchAll();

$formatted = array();
foreach ($matches as $m) {
    $formatted[] = array(
        'id' => (int) $m['idUser'],
        'name' => trim($m['prenomUser'] . ' ' . $m['nomEUser']),
        'photo' => $m['photo'],
        'age' => $m['age'] !== null ? (int) $m['age'] : null,
        'bio' => $m['biographie'],
        'genre' => $m['libGenr'],
        'lastComment' => $m['lastComment']
    );
}

echo json_encode(array('success' => true, 'matches' => $formatted));
