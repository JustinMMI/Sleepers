<?php

require_once dirname(__DIR__) . '/bootstrap.php';

// Vérification de connexion membre
if (!defined('ID_USER')) {
    http_response_code(401);
    exit('Connexion requise pour modifier son profil.');
}

$bedType = trim($_POST['bed_type'] ?? '');
$napDuration = trim($_POST['nap_duration'] ?? '30 min');
$dreamer = !empty($_POST['dreamer']) && $_POST['dreamer'] == '1';
$sleepSound = trim($_POST['sleep_sound'] ?? 'Silence');
$pillowCount = trim($_POST['pillow_count'] ?? '2 oreillers');
$wakeUpStyle = trim($_POST['wake_up_style'] ?? 'Réveil en douceur');
$idealPartner = trim($_POST['ideal_partner'] ?? '');

// 1. Gestion de la mise à jour de la photo de profil
$photoToSave = null;

// Option A : Fichier image envoyé
if (!empty($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['photo_file']['tmp_name'];
    $fileName = $_FILES['photo_file']['name'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
        $uploadDir = ROOT . '/src/images/profiles';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }
        $newFileName = 'u' . ID_USER . '_' . time() . '.' . $ext;
        $destPath = $uploadDir . '/' . $newFileName;
        
        if (move_uploaded_file($fileTmp, $destPath)) {
            $photoToSave = 'src/images/profiles/' . $newFileName;
        }
    }
}

// Option B : Avatar prédéfini sélectionné
if (!$photoToSave && !empty($_POST['avatar_preset'])) {
    $preset = trim((string)$_POST['avatar_preset']);
    if ($preset !== '') {
        $photoToSave = $preset;
    }
}

// Option C : URL personnalisée
if (!$photoToSave && !empty($_POST['photo_url'])) {
    $customUrl = trim((string)$_POST['photo_url']);
    if ($customUrl !== '') {
        $photoToSave = $customUrl;
    }
}

// Si une photo a été choisie, mettre à jour la colonne photo dans la table USER
if ($photoToSave !== null) {
    // La colonne photo dans USER fait varchar(50)
    $dbPhotoVal = mb_substr($photoToSave, 0, 50);
    api_execute(
        'UPDATE `USER` SET photo = :photo WHERE idUser = :id',
        array(
            ':photo' => $dbPhotoVal,
            ':id' => ID_USER
        )
    );
}

// 2. Gestion des tags de sieste
$habits = [];
if (!empty($_POST['habits']) && is_array($_POST['habits'])) {
    foreach ($_POST['habits'] as $h) {
        $clean = trim((string)$h);
        if ($clean !== '' && !in_array($clean, $habits, true)) {
            $habits[] = $clean;
        }
    }
}

// Ajout d'un tag personnalisé si fourni
if (!empty($_POST['custom_tag'])) {
    $custom = trim((string)$_POST['custom_tag']);
    if ($custom !== '' && !in_array($custom, $habits, true)) {
        $habits[] = '✨ ' . $custom;
    }
}

// Compléments de tags si moins de 3
if (!empty($napDuration) && !in_array('⏱️ ' . $napDuration, $habits, true)) {
    $habits[] = '⏱️ ' . $napDuration;
}
if ($dreamer) {
    if (!in_array('💤 Grand rêveur', $habits, true)) {
        $habits[] = '💤 Grand rêveur';
    }
} else {
    if (!in_array('😴 Sommeil sans rêve', $habits, true)) {
        $habits[] = '😴 Sommeil sans rêve';
    }
}

$traits = [
    'bed_type' => $bedType !== '' ? $bedType : 'Lit douillet & accueillant',
    'nap_duration' => $napDuration,
    'dreamer' => $dreamer,
    'sleep_habits' => array_values(array_unique($habits)),
    'sleep_sound' => $sleepSound,
    'pillow_count' => $pillowCount,
    'wake_up_style' => $wakeUpStyle,
    'ideal_partner' => $idealPartner
];

if ($photoToSave !== null) {
    $traits['photo'] = $photoToSave;
}

save_user_sleep_traits(ID_USER, $traits);

header('Location: ' . ROOT_URL . '/views/backend/security/profile.php?saved=1');
exit;
