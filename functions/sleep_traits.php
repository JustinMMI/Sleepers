<?php

/**
 * Fonctions de gestion des données étendues en JSON (préférences de sieste & paramètres applicatifs)
 * Ne modifie aucunement la base de données SQL ni la structure des tables.
 */

if (!defined('SLEEP_DATA_DIR')) {
    define('SLEEP_DATA_DIR', dirname(__DIR__) . '/BDD');
}

/**
 * Récupère le chemin d'un fichier JSON de données
 */
function sleep_data_file(string $filename): string {
    if (!is_dir(SLEEP_DATA_DIR)) {
        @mkdir(SLEEP_DATA_DIR, 0777, true);
    }
    return SLEEP_DATA_DIR . '/' . ltrim($filename, '/');
}

/**
 * Charge les paramètres généraux de l'application (ex: activation du test BDD)
 */
function load_app_settings(): array {
    $file = sleep_data_file('app_settings.json');
    if (!file_exists($file)) {
        return ['test_bdd_enabled' => false];
    }
    $raw = @file_get_contents($file);
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return ['test_bdd_enabled' => false];
    }
    if (!isset($data['test_bdd_enabled'])) {
        $data['test_bdd_enabled'] = false;
    }
    return $data;
}

/**
 * Sauvegarde les paramètres généraux de l'application
 */
function save_app_settings(array $settings): bool {
    $file = sleep_data_file('app_settings.json');
    $json = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents($file, $json, LOCK_EX) !== false;
}

/**
 * Indique si le sélecteur de test BDD est activé sur l'accueil (désactivé par défaut)
 */
function is_test_bdd_enabled(): bool {
    $settings = load_app_settings();
    return !empty($settings['test_bdd_enabled']);
}

/**
 * Charge l'ensemble des profils de sieste stockés dans le JSON
 */
function load_sleep_profiles(): array {
    $file = sleep_data_file('sleep_profiles.json');
    if (!file_exists($file)) {
        return [];
    }
    $raw = @file_get_contents($file);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/**
 * Récupère les caractéristiques de sieste d'un utilisateur depuis le JSON
 * ou génère des valeurs cohérentes basées sur sa fiche BDD s'il n'a pas encore personnalisé.
 */
function get_user_sleep_traits(int $userId, string $defaultPhoto = '', string $defaultBio = ''): array {
    $profiles = load_sleep_profiles();
    $idKey = (string)$userId;

    if (isset($profiles[$idKey]) && is_array($profiles[$idKey])) {
        return $profiles[$idKey];
    }

    // Valeurs par défaut si le profil n'a pas encore personnalisé sa sieste
    $bedType = 'Lit douillet & accueillant';
    if (!empty($defaultPhoto) && !str_starts_with($defaultPhoto, 'http') && !preg_match('/\.(jpg|jpeg|png|webp)$/i', $defaultPhoto)) {
        $bedType = trim($defaultPhoto);
    }

    return [
        'bed_type' => $bedType,
        'nap_duration' => '30 min',
        'dreamer' => true,
        'sleep_habits' => ['🛌 Plaid polaire', '⏱️ 30 min', '💤 Rêveur', '☕ Tisane camomille'],
        'sleep_sound' => '🌧️ Bruit de pluie',
        'pillow_count' => '2 oreillers moelleux',
        'wake_up_style' => 'Murmures en douceur et étirements',
        'ideal_partner' => 'Partenaire calme pour une sieste réparatrice.'
    ];
}

/**
 * Enregistre les caractéristiques personnalisées d'un utilisateur dans le JSON
 */
function save_user_sleep_traits(int $userId, array $traits): bool {
    $file = sleep_data_file('sleep_profiles.json');
    $profiles = load_sleep_profiles();
    $idKey = (string)$userId;

    // Nettoyage et normalisation des tags d'habitudes
    $habits = [];
    if (!empty($traits['sleep_habits']) && is_array($traits['sleep_habits'])) {
        foreach ($traits['sleep_habits'] as $h) {
            $cleaned = trim((string)$h);
            if ($cleaned !== '' && !in_array($cleaned, $habits, true)) {
                $habits[] = $cleaned;
            }
        }
    }

    // Si aucun tag sélectionné, en construire à partir des réponses
    if (empty($habits)) {
        if (!empty($traits['bed_type'])) {
            $habits[] = '🛏️ ' . mb_substr($traits['bed_type'], 0, 24);
        }
        if (!empty($traits['nap_duration'])) {
            $habits[] = '⏱️ ' . $traits['nap_duration'];
        }
        $habits[] = !empty($traits['dreamer']) ? '💤 Grand rêveur' : '😴 Sommeil sans rêve';
        if (!empty($traits['sleep_sound'])) {
            $habits[] = $traits['sleep_sound'];
        }
    }

    $profiles[$idKey] = [
        'bed_type' => trim($traits['bed_type'] ?? 'Lit douillet'),
        'nap_duration' => trim($traits['nap_duration'] ?? '30 min'),
        'dreamer' => !empty($traits['dreamer']),
        'sleep_habits' => array_slice($habits, 0, 5),
        'sleep_sound' => trim($traits['sleep_sound'] ?? 'Silence'),
        'pillow_count' => trim($traits['pillow_count'] ?? '2 oreillers'),
        'wake_up_style' => trim($traits['wake_up_style'] ?? 'Réveil en douceur'),
        'ideal_partner' => trim($traits['ideal_partner'] ?? '')
    ];

    $json = json_encode($profiles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents($file, $json, LOCK_EX) !== false;
}

