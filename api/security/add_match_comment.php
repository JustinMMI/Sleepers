<?php

require_once dirname(__DIR__) . '/bootstrap.php';

if (!defined('ID_USER')) {
    api_error(401, 'Connexion requise pour cette action.');
}

$targetId = (int) api_input('targetId');
$comment = trim((string) api_input('comment'));

if ($targetId <= 0) {
    api_error(400, 'Identifiant cible invalide.');
}

if ($comment === '') {
    api_error(400, 'Le commentaire ne peut pas être vide.');
}

// Tronquer à 300 caractères conformément au schéma SQL de COMMENTS (libComment varchar(300))
$comment = mb_substr($comment, 0, 300);

// Vérification stricte : la personne doit impérativement être un match réciproque dans la table MATCHS
$matchCheck = api_execute(
    'SELECT COUNT(*) FROM MATCHS WHERE (idUserM1 = :m1 AND idUserM2 = :m2) OR (idUserM1 = :m3 AND idUserM2 = :m4)',
    array(
        ':m1' => ID_USER,
        ':m2' => $targetId,
        ':m3' => $targetId,
        ':m4' => ID_USER
    )
)->fetchColumn();

if (!$matchCheck) {
    api_error(403, 'Vous ne pouvez ajouter un commentaire que sur une personne avec qui vous avez un match.');
}

// Insertion ou mise à jour du commentaire dans la table COMMENTS
api_execute(
    'INSERT INTO COMMENTS (idUserC1, idUserC2, libComment) VALUES (:c1, :c2, :comm1)
     ON DUPLICATE KEY UPDATE libComment = :comm2',
    array(
        ':c1' => ID_USER,
        ':c2' => $targetId,
        ':comm1' => $comment,
        ':comm2' => $comment
    )
);

// Récupérer le nom de l'auteur pour retour immédiat
$author = api_execute(
    'SELECT prenomUser FROM `USER` WHERE idUser = :id',
    array(':id' => ID_USER)
)->fetch();

api_json(array(
    'success' => true,
    'targetId' => $targetId,
    'comment' => $comment,
    'authorName' => $author['prenomUser'] ?? 'Vous',
    'message' => 'Commentaire enregistré avec succès.'
));

