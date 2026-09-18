<?php

require_once dirname(__DIR__) . '/bootstrap.php';

if (!defined('ID_USER')) {
    api_error(401, 'Connexion requise pour cette action.');
}

$stmt = api_execute(
    'SELECT u.idUser, u.nomEUser, u.prenomUser, u.age, u.photo, u.biographie, g.libGenr,
            (SELECT COUNT(*) FROM MATCHS m 
             WHERE (m.idUserM1 = :m1 AND m.idUserM2 = u.idUser) 
                OR (m.idUserM1 = u.idUser AND m.idUserM2 = :m2)) AS is_matched
     FROM LIKES l
     JOIN `USER` u ON l.idUserL2 = u.idUser
     LEFT JOIN GENRE g ON u.idGenr = g.idGenr
     WHERE l.idUserL1 = :myId AND l.likeL1 = 1
     ORDER BY u.prenomUser ASC',
    array(
        ':m1' => ID_USER,
        ':m2' => ID_USER,
        ':myId' => ID_USER
    )
);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$result = array();

foreach ($rows as $r) {
    $uid = (int) $r['idUser'];
    $traits = get_user_sleep_traits($uid, $r['photo'] ?? '', $r['biographie'] ?? '');

    $photoUrl = '';
    if (!empty($r['photo']) && (str_starts_with($r['photo'], 'http') || preg_match('/\.(jpg|jpeg|png|webp)$/i', $r['photo']))) {
        $photoUrl = str_starts_with($r['photo'], 'http') ? $r['photo'] : ROOT_URL . '/' . ltrim($r['photo'], '/');
    } else {
        $photoUrl = 'https://i.pravatar.cc/150?u=sleepers_' . $uid;
    }

    $result[] = array(
        'id' => $uid,
        'name' => trim(($r['prenomUser'] ?? '') . ' ' . ($r['nomEUser'] ?? '')),
        'prenomUser' => $r['prenomUser'] ?? 'Siesteur',
        'age' => $r['age'] ? (int)$r['age'] : null,
        'genre' => $r['libGenr'] ?? 'Sieste',
        'photo' => $photoUrl,
        'bedType' => $traits['bed_type'] ?? 'Lit douillet',
        'napDuration' => $traits['nap_duration'] ?? '30 min',
        'isMatched' => !empty($r['is_matched'])
    );
}

api_json(array(
    'success' => true,
    'liked' => $result,
    'count' => count($result)
));

