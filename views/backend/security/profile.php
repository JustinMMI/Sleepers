<?php
include '../../../header.php';

if (!defined('ID_USER')) {
    header('Location: ' . ROOT_URL . '/views/backend/security/login.php');
    exit;
}

sql_connect();
global $DB;

$stmt = $DB->prepare('SELECT u.*, g.libGenr FROM `USER` u LEFT JOIN GENRE g ON u.idGenr = g.idGenr WHERE u.idUser = :id');
$stmt->execute(array(':id' => ID_USER));
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo '<div class="container py-5"><div class="alert alert-danger">Profil introuvable.</div></div>';
    include '../../../footer.php';
    exit;
}

// Récupération des préférences actuelles
$traits = get_user_sleep_traits((int)ID_USER, $user['photo'] ?? '', $user['biographie'] ?? '');

$availableHabits = [
    '🛌 Plaid polaire géant',
    '☕ Tisane camomille',
    '🧸 Doudou réconfortant',
    '📖 Lecture avant dodo',
    '🧦 Chaussettes doudou',
    '🎧 Casque anti-bruit',
    '🕯️ Bougie parfumée',
    '🧊 Chambre fraîche',
    '☀️ Sieste au soleil',
    '🐈 Chat sur la couette',
    '🌊 Bruit des vagues',
    '🌧️ Pluie sur velux'
];

$userHabits = $traits['sleep_habits'] ?? [];

// Résolution de la photo actuelle
$currentPhoto = '';
if (!empty($user['photo']) && (str_starts_with($user['photo'], 'http') || preg_match('/\.(jpg|jpeg|png|webp)$/i', $user['photo']))) {
    $currentPhoto = str_starts_with($user['photo'], 'http') ? $user['photo'] : ROOT_URL . '/' . ltrim($user['photo'], '/');
} else {
    $currentPhoto = 'https://i.pravatar.cc/500?u=sleepers_' . (int)ID_USER;
}
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">

            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h1 class="fw-bold text-white mb-1">🛏️ Mon Profil Sieste</h1>
                    <p class="text-white-50 mb-0">Personnalisez votre photo et vos habitudes de sommeil pour les autres membres</p>
                </div>
                <div>
                    <a href="<?php echo ROOT_URL; ?>/" class="btn btn-outline-light btn-sm">← Retour aux profils</a>
                </div>
            </div>

            <?php if (isset($_GET['saved'])) { ?>
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                    <span class="fs-5">✓</span>
                    <div>
                        <strong>Modifications enregistrées !</strong>
                        Votre photo et vos préférences de sieste ont été mises à jour.
                    </div>
                </div>
            <?php } ?>

            <div class="row g-4">
                <!-- Colonne Formulaire -->
                <div class="col-12 col-md-7">
                    <div class="card p-4 shadow-lg" style="background: var(--sleep-bg-card); border: 1px solid var(--sleep-border-purple);">
                        <h4 class="fw-bold text-white mb-3">✨ Personnaliser mes informations</h4>

                        <form action="<?php echo ROOT_URL; ?>/api/security/save_profile.php" method="POST" enctype="multipart/form-data">

                            <!-- Section Photo de profil -->
                            <div class="mb-4 pb-3 border-bottom border-secondary">
                                <label class="form-label fw-bold text-white">📸 Photo de profil</label>
                                
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <img src="<?php echo $currentPhoto; ?>" alt="Votre photo" class="rounded-circle object-fit-cover border border-2 border-purple-400" style="width: 72px; height: 72px;" />
                                    <div>
                                        <div class="small fw-semibold text-white">Photo actuelle</div>
                                        <div class="small text-white-50">Vous pouvez importer une nouvelle photo ou choisir un avatar ci-dessous.</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="photo_file" class="form-label small text-white-50">Importer depuis votre appareil (JPG, PNG, WebP) :</label>
                                    <input type="file" id="photo_file" name="photo_file" class="form-control form-control-sm" accept="image/*" />
                                </div>

                                <div>
                                    <label class="form-label small text-white-50 d-block">Ou choisir un avatar de siesteur :</label>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <?php 
                                        $presets = [
                                            'https://i.pravatar.cc/300?u=sleep_cozy1' => '😴 Zen',
                                            'https://i.pravatar.cc/300?u=sleep_cozy2' => '🛌 Douillet',
                                            'https://i.pravatar.cc/300?u=sleep_cozy3' => '✨ Rêveur',
                                            'https://i.pravatar.cc/300?u=sleep_cozy4' => '☕ Calme',
                                            'https://i.pravatar.cc/300?u=sleep_cozy5' => '🌧️ Pluvieux',
                                            'https://i.pravatar.cc/300?u=sleep_cozy6' => '💤 Paisible'
                                        ];
                                        foreach ($presets as $url => $name) { ?>
                                            <label class="d-flex flex-column align-items-center p-1 rounded-3 border border-secondary" style="cursor: pointer; width: 70px;">
                                                <input type="radio" name="avatar_preset" value="<?php echo $url; ?>" class="mb-1" />
                                                <img src="<?php echo $url; ?>" class="rounded-circle object-fit-cover mb-1" style="width: 38px; height: 38px;" />
                                                <span style="font-size: 0.65rem;" class="text-white-50"><?php echo $name; ?></span>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Type de lit / Literie -->
                            <div class="mb-3">
                                <label for="bed_type" class="form-label">🛏️ Description de votre literie</label>
                                <input type="text" id="bed_type" name="bed_type" class="form-control" value="<?php echo htmlspecialchars($traits['bed_type'] ?? '', ENT_QUOTES); ?>" placeholder="Ex: Grand lit king size, plaid polaire et couette épaisse" required />
                                <div class="form-text text-white-50 small">S'affiche sur votre carte pour trouver un siesteur compatible.</div>
                            </div>

                            <!-- Durée de sieste préférée -->
                            <div class="mb-3">
                                <label for="nap_duration" class="form-label">⏱️ Durée de sieste idéale</label>
                                <select id="nap_duration" name="nap_duration" class="form-select">
                                    <?php 
                                    $durations = [
                                        '15 min' => '15 min (Flash booster)',
                                        '20 min' => '20 min (Micro-sieste idéale)',
                                        '30 min' => '30 min (Express réparateur)',
                                        '45 min' => '45 min (Classique équilibré)',
                                        '1h00' => '1 heure (Sieste royale)',
                                        '1h30' => '1h30 (Cycle de sommeil complet)',
                                        '2h00+' => '2 heures et plus (Sieste marathon)'
                                    ];
                                    $currentDuration = $traits['nap_duration'] ?? '30 min';
                                    foreach ($durations as $val => $label) {
                                        $sel = (strpos($currentDuration, $val) !== false || $currentDuration === $val) ? 'selected' : '';
                                        echo "<option value=\"$val\" $sel>$label</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Grand Rêveur ou Sommeil de plomb -->
                            <div class="mb-3">
                                <label class="form-label d-block">💤 Type de sommeil</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="dreamer" id="dreamer_yes" value="1" <?php echo !empty($traits['dreamer']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label text-white" for="dreamer_yes">
                                            ✨ Grand rêveur (plein de rêves)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="dreamer" id="dreamer_no" value="0" <?php echo empty($traits['dreamer']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label text-white" for="dreamer_no">
                                            😴 Sommeil profond sans rêve
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Ambiance sonore -->
                            <div class="mb-3">
                                <label for="sleep_sound" class="form-label">🎧 Ambiance sonore favorite</label>
                                <select id="sleep_sound" name="sleep_sound" class="form-select">
                                    <?php
                                    $sounds = [
                                        '🌧️ Bruit de pluie' => '🌧️ Bruit de pluie relaxant',
                                        '🔇 Silence absolu' => '🔇 Silence total et absolu',
                                        '🎧 Musique lo-fi' => '🎧 Musique lo-fi / piano doux',
                                        '🐱 Ronronnement de chat' => '🐱 Ronronnement de chat apaisant',
                                        '🌊 Vagues de l\'océan' => '🌊 Vagues de l\'océan',
                                        '🔥 Feu de cheminée' => '🔥 Crépitement d\'un feu de bois',
                                        '🍃 Vent dans les feuilles' => '🍃 Murmure du vent dans les arbres'
                                    ];
                                    $curSound = $traits['sleep_sound'] ?? '';
                                    foreach ($sounds as $val => $label) {
                                        $sel = (strpos($curSound, $val) !== false || $curSound === $val) ? 'selected' : '';
                                        echo "<option value=\"$val\" $sel>$label</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Nombre d'oreillers -->
                            <div class="mb-3">
                                <label for="pillow_count" class="form-label">🛌 Exigence oreillers</label>
                                <select id="pillow_count" name="pillow_count" class="form-select">
                                    <?php
                                    $pillows = [
                                        '1 oreiller plat' => '1 oreiller plat ou ergonomique',
                                        '2 oreillers moelleux' => '2 oreillers moelleux classiques',
                                        '3 oreillers' => '3 oreillers pour bien caler la tête',
                                        '4+ oreillers géants' => '4+ oreillers géants (montagne de plumes)'
                                    ];
                                    $curPillow = $traits['pillow_count'] ?? '';
                                    foreach ($pillows as $val => $label) {
                                        $sel = (strpos($curPillow, $val) !== false || $curPillow === $val) ? 'selected' : '';
                                        echo "<option value=\"$val\" $sel>$label</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Style de réveil -->
                            <div class="mb-3">
                                <label for="wake_up_style" class="form-label">☀️ Réveil souhaité</label>
                                <select id="wake_up_style" name="wake_up_style" class="form-select">
                                    <?php
                                    $wakes = [
                                        '☕ Café chaud au lit' => '☕ Café ou thé chaud apporté au lit',
                                        'Murmures et étirements' => 'Murmures calmes et étirements au ralenti',
                                        'Lumière progressive' => 'Lumière tamisée progressive',
                                        'Sans alarme' => 'Sans réveil, au feeling'
                                    ];
                                    $curWake = $traits['wake_up_style'] ?? '';
                                    foreach ($wakes as $val => $label) {
                                        $sel = (strpos($curWake, $val) !== false || $curWake === $val) ? 'selected' : '';
                                        echo "<option value=\"$val\" $sel>$label</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Tags de sieste (Habits) -->
                            <div class="mb-3">
                                <label class="form-label d-block">🏷️ Vos habitudes clés</label>
                                <div class="row g-2 mb-2">
                                    <?php foreach ($availableHabits as $h) { 
                                        $checked = in_array($h, $userHabits, true) ? 'checked' : '';
                                    ?>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="habits[]" value="<?php echo htmlspecialchars($h, ENT_QUOTES); ?>" id="h_<?php echo md5($h); ?>" <?php echo $checked; ?>>
                                                <label class="form-check-label text-white small" for="h_<?php echo md5($h); ?>">
                                                    <?php echo htmlspecialchars($h); ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <input type="text" name="custom_tag" class="form-control form-control-sm mt-2" placeholder="Ajouter un tag personnalisé (ex: Chaussettes polaires)" />
                            </div>

                            <!-- Partenaire idéal -->
                            <div class="mb-4">
                                <label for="ideal_partner" class="form-label">💤 Votre partenaire de sieste idéal</label>
                                <textarea id="ideal_partner" name="ideal_partner" class="form-control" rows="2" placeholder="Ex: Quelqu'un qui ne ronfle pas et aime le bruit de la pluie..."><?php echo htmlspecialchars($traits['ideal_partner'] ?? '', ENT_QUOTES); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-sleep-primary w-100 py-2 fs-6">
                                <span>💾 Enregistrer les modifications</span>
                            </button>

                        </form>
                    </div>
                </div>

                <!-- Colonne Aperçu Carte -->
                <div class="col-12 col-md-5">
                    <div class="sticky-top" style="top: 85px;">
                        <h5 class="fw-bold text-white mb-2">👁️ Aperçu de votre carte</h5>
                        <p class="text-white-50 small mb-3">Voici comment votre profil apparaît pour les autres siesteurs :</p>

                        <div class="tinder-profile-card position-relative" style="height: 480px;">
                            <img src="<?php echo $currentPhoto; ?>" alt="Votre photo" class="tinder-card-photo" />
                            <div class="tinder-card-overlay"></div>

                            <div class="tinder-badge-top">
                                <span class="tinder-tag">Siesteur</span>
                                <span class="tinder-tag tinder-tag-genre"><?php echo htmlspecialchars($user['libGenr'] ?? 'Sieste'); ?></span>
                            </div>

                            <div class="tinder-card-content">
                                <div class="tinder-pill-bed">
                                    🛏️ Literie : <strong><?php echo htmlspecialchars($traits['bed_type'] ?? 'Lit douillet'); ?></strong>
                                </div>
                                <div class="tinder-profile-name">
                                    <?php echo htmlspecialchars($user['prenomUser'] . ' ' . ($user['nomEUser'] ?? '')); ?>,
                                    <span class="tinder-profile-age"><?php echo (int)($user['age'] ?? 25); ?> ans</span>
                                </div>
                                <div class="tinder-profile-bio">
                                    <?php echo htmlspecialchars(!empty($user['biographie']) ? $user['biographie'] : ($traits['ideal_partner'] ?? 'Prêt pour une sieste au calme.')); ?>
                                </div>
                                <div class="tinder-pill-habits">
                                    <?php foreach (($traits['sleep_habits'] ?? ['🛌 Plaid', '⏱️ 30 min', '💤 Rêveur']) as $h) { ?>
                                        <span class="tinder-pill-habit"><?php echo htmlspecialchars($h); ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</main>

<?php include '../../../footer.php'; ?>
