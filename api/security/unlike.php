<?php

require_once dirname(__DIR__) . '/bootstrap.php';

if (!defined('ID_USER')) {
    api_error(401, 'Connexion requise pour cette action.');
}

$targetId = (int) api_input('targetId');

if ($targetId <= 0) {
    api_error(400, 'Identifiant cible invalide.');
}

// 1. Supprimer le like dans la table LIKES
api_execute(
    'DELETE FROM LIKES WHERE idUserL1 = :myId AND idUserL2 = :targetId',
    array(
        ':myId' => ID_USER,
        ':targetId' => $targetId
    )
);

// 2. Supprimer l'éventuel match dans la table MATCHS
api_execute(
    'DELETE FROM MATCHS WHERE (idUserM1 = :m1 AND idUserM2 = :m2) OR (idUserM1 = :m3 AND idUserM2 = :m4)',
    array(
        ':m1' => ID_USER,
        ':m2' => $targetId,
        ':m3' => $targetId,
        ':m4' => ID_USER
    )
);

api_json(array(
    'success' => true,
    'targetId' => $targetId,
    'message' => 'Le like a été retiré avec succès.'
));

