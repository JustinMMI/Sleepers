<?php

require_once dirname(__DIR__) . '/bootstrap.php';

if (!defined('ID_USER')) {
    api_error(401, 'Connexion requise pour cette action.');
}

// Récupérer tous les profils matchés avec l'utilisateur connecté
$matchesStmt = api_execute(
    'SELECT u.idUser, u.nomEUser, u.prenomUser, u.age, u.photo, u.biographie, g.libGenr
     FROM MATCHS m
     JOIN `USER` u ON (u.idUser = CASE WHEN m.idUserM1 = :m1 THEN m.idUserM2 ELSE m.idUserM1 END)
     LEFT JOIN GENRE g ON u.idGenr = g.idGenr
     WHERE m.idUserM1 = :m2 OR m.idUserM2 = :m3
     GROUP BY u.idUser
     ORDER BY u.prenomUser ASC',
    array(
        ':m1' => ID_USER,
        ':m2' => ID_USER,
        ':m3' => ID_USER
    )
);

$matches = $matchesStmt->fetchAll(PDO::FETCH_ASSOC);
$result = array();

foreach ($matches as $m) {
    $uid = (int) $m['idUser'];
    $traits = get_user_sleep_traits($uid, $m['photo'] ?? '', $m['biographie'] ?? '');

    // Résolution photo
    $photoUrl = '';
    if (!empty($m['photo']) && (str_starts_with($m['photo'], 'http') || preg_match('/\.(jpg|jpeg|png|webp)$/i', $m['photo']))) {
        $photoUrl = str_starts_with($m['photo'], 'http') ? $m['photo'] : ROOT_URL . '/' . ltrim($m['photo'], '/');
    } else {
        $photoUrl = 'https://i.pravatar.cc/150?u=sleepers_' . $uid;
    }

    // Récupérer les commentaires laissés sur ce profil (table COMMENTS)
    $commentsStmt = api_execute(
        'SELECT c.idUserC1, c.idUserC2, c.libComment, uAuthor.prenomUser AS authorName
         FROM COMMENTS c
         LEFT JOIN `USER` uAuthor ON c.idUserC1 = uAuthor.idUser
         WHERE c.idUserC2 = :targetId
         ORDER BY c.idUserC1 ASC',
        array(':targetId' => $uid)
    );
    $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Indiquer si l'utilisateur connecté a déjà laissé un commentaire sur ce match
    $myComment = null;
    foreach ($comments as $c) {
        if ((int)$c['idUserC1'] === (int)ID_USER) {
            $myComment = $c['libComment'];
            break;
        }
    }

    $result[] = array(
        'id' => $uid,
        'name' => trim(($m['prenomUser'] ?? '') . ' ' . ($m['nomEUser'] ?? '')),
        'prenomUser' => $m['prenomUser'] ?? 'Siesteur',
        'age' => $m['age'] ? (int)$m['age'] : null,
        'genre' => $m['libGenr'] ?? 'Sieste',
        'photo' => $photoUrl,
        'bedType' => $traits['bed_type'] ?? 'Lit douillet',
        'napDuration' => $traits['nap_duration'] ?? '30 min',
        'dreamer' => !empty($traits['dreamer']),
        'comments' => $comments,
        'myComment' => $myComment
    );
}

api_json(array(
    'success' => true,
    'matches' => $result,
    'count' => count($result)
));

