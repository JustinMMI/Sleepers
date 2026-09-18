<?php 
define('HIDE_FOOTER', true);
require_once 'header.php';
sql_connect();
global $DB;

$isLoggedIn = defined('ID_USER');
$currentUser = null;
$dbProfiles = array();
$allUsersList = array();

// Mode démo (activé/désactivé depuis le panel admin)
$isTestBddEnabled = function_exists('is_test_bdd_enabled') ? is_test_bdd_enabled() : false;
if ($isTestBddEnabled) {
    try {
        $allUsersStmt = $DB->query('SELECT idUser, prenomUser, nomEUser, age FROM `USER` ORDER BY idUser ASC');
        $allUsersList = $allUsersStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $allUsersList = array();
    }
}

if ($isLoggedIn) {
    // Profil membre connecté
    $stmt = $DB->prepare('SELECT u.*, g.libGenr FROM `USER` u LEFT JOIN GENRE g ON u.idGenr = g.idGenr WHERE u.idUser = :id');
    $stmt->execute(array(':id' => ID_USER));
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

    // Profils à découvrir (exclut soi-même et les profils déjà likés/passés dans LIKES)
    $stmtProfiles = $DB->prepare(
        'SELECT u.idUser, u.nomEUser, u.prenomUser, u.age, u.photo, u.biographie, g.libGenr
         FROM `USER` u
         LEFT JOIN GENRE g ON u.idGenr = g.idGenr
         WHERE u.idUser <> :myId
           AND u.idUser NOT IN (SELECT idUserL2 FROM LIKES WHERE idUserL1 = :myId)
         ORDER BY u.idUser ASC'
    );
    $stmtProfiles->execute(array(':myId' => ID_USER));
    $dbProfiles = $stmtProfiles->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Mode Découverte pour les visiteurs non connectés
    $stmtProfiles = $DB->query(
        'SELECT u.idUser, u.nomEUser, u.prenomUser, u.age, u.photo, u.biographie, g.libGenr
         FROM `USER` u
         LEFT JOIN GENRE g ON u.idGenr = g.idGenr
         ORDER BY u.idUser ASC'
    );
    $dbProfiles = $stmtProfiles->fetchAll(PDO::FETCH_ASSOC);
}

// Rattachement des préférences de sieste pour chaque profil
foreach ($dbProfiles as &$p) {
    $p['sleep_traits'] = get_user_sleep_traits((int)$p['idUser'], $p['photo'] ?? '', $p['biographie'] ?? '');
}
unset($p);

// Résolution de la photo de l'utilisateur connecté
$currentUserPhoto = '';
if ($isLoggedIn && !empty($currentUser)) {
    if (!empty($currentUser['photo']) && (str_starts_with($currentUser['photo'], 'http') || preg_match('/\.(jpg|jpeg|png|webp)$/i', $currentUser['photo']))) {
        $currentUserPhoto = str_starts_with($currentUser['photo'], 'http') ? $currentUser['photo'] : ROOT_URL . '/' . ltrim($currentUser['photo'], '/');
    } else {
        $currentUserPhoto = 'https://i.pravatar.cc/150?u=sleepers_' . (int)ID_USER;
    }
}
?>

<div class="sleepers-app-container">

  <?php if ($isLoggedIn) { ?>
    <!-- =========================================================================
         BARRE SUPÉRIEURE MOBILE (< 992px)
         ========================================================================= -->
    <div class="sleepers-mobile-topbar">
      <div class="d-flex align-items-center gap-2">
        <span class="fs-5">🌙💤</span>
        <span class="fw-bold text-white fs-6">Sleepers</span>
      </div>

      <!-- Switcher mobile Deck / Matchs & Likes -->
      <div class="d-flex gap-1">
        <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-2 py-1" id="mobileBtnDeck" onclick="toggleMobileView('deck')">
          <span>🔥 Deck</span>
        </button>
        <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-2 py-1" id="mobileBtnMatches" onclick="toggleMobileView('sidebar')">
          <span>💬 Matchs</span>
        </button>
      </div>

      <div class="d-flex align-items-center gap-1">
        <a href="<?php echo ROOT_URL; ?>/views/backend/security/profile.php" class="sleepers-icon-btn" title="Modifier mon profil">
          <img src="<?php echo $currentUserPhoto; ?>" class="rounded-circle object-fit-cover" style="width: 24px; height: 24px;" alt="Profil" />
        </a>
        <a href="<?php echo ROOT_URL; ?>/views/backend/dashboard.php" class="sleepers-icon-btn" title="Panneau Admin">
          ⚙️
        </a>
        <a href="<?php echo ROOT_URL; ?>/api/security/disconnect.php" class="sleepers-icon-btn" title="Déconnexion">
          ⎋
        </a>
      </div>
    </div>

    <!-- =========================================================================
         VOLET LATÉRAL GAUCHE : LOGO, PROFIL, MATCHS & LIKES
         ========================================================================= -->
    <aside class="sleepers-sidebar" id="sleepersSidebar">

      <!-- En-tête Sidebar : Logo + Profil + Admin + Déconnexion -->
      <div class="sleepers-sidebar-header">
        <div class="d-flex align-items-center gap-2">
          <a href="<?php echo ROOT_URL; ?>/views/backend/security/profile.php" class="sleepers-user-badge text-decoration-none" title="Modifier mon profil">
            <img src="<?php echo $currentUserPhoto; ?>" class="sleepers-user-avatar" alt="Photo" />
            <span class="fw-bold text-white small text-truncate" style="max-width: 100px;">
              <?php echo htmlspecialchars($currentUser['prenomUser'] ?? 'Mon Profil', ENT_QUOTES); ?>
            </span>
          </a>
        </div>

        <div class="sleepers-brand-logo">
          <span>🌙💤</span>
          <span>Sleepers</span>
        </div>

        <div class="sleepers-header-actions">
          <a href="<?php echo ROOT_URL; ?>/views/backend/dashboard.php" class="sleepers-icon-btn" title="Panel Admin">
            ⚙️
          </a>
          <a href="<?php echo ROOT_URL; ?>/api/security/disconnect.php" class="sleepers-icon-btn" title="Déconnexion">
            ⎋
          </a>
        </div>
      </div>

      <!-- Onglets Flat : Mes Matchs & Profils Likés -->
      <div class="sleepers-tabs">
        <button type="button" class="sleepers-tab-trigger active" id="tabBtnMatches" onclick="switchSideTab('matches')">
          <span>💤 Mes Matchs</span>
          <span id="badgeMatchesCount" class="sleepers-tab-badge">0</span>
        </button>
        <button type="button" class="sleepers-tab-trigger" id="tabBtnLiked" onclick="switchSideTab('liked')">
          <span>❤️ Profils Likés</span>
          <span id="badgeLikedCount" class="sleepers-tab-badge">0</span>
        </button>
      </div>

      <!-- Corps de la sidebar avec scroll autonome -->
      <div class="sleepers-sidebar-content">
        
        <!-- Onglet 1 : Matchs et avis -->
        <div id="tabContentMatches">
          <div id="matchesContainer">
            <div class="text-center py-5 text-secondary small">Chargement des matchs...</div>
          </div>
        </div>

        <!-- Onglet 2 : Profils likés avec bouton Unlike -->
        <div id="tabContentLiked" class="d-none">
          <div id="likedContainer">
            <div class="text-center py-5 text-secondary small">Chargement des profils likés...</div>
          </div>
        </div>

      </div>

    </aside>

    <!-- =========================================================================
         ZONE PRINCIPALE : DECK DE SWIPE TINDER
         ========================================================================= -->
    <main class="sleepers-main-area">

      <?php if ($isTestBddEnabled) { ?>
        <!-- Mode Démo (si activé dans le panel admin) -->
        <div class="sleepers-test-bar mb-3">
          <span class="small text-secondary fw-semibold">🧪 Mode Démo :</span>
          <form action="<?php echo ROOT_URL; ?>/api/security/quick_switch.php" method="POST" class="d-inline-flex align-items-center gap-2 m-0">
            <select name="userId" aria-label="Choisir un profil de test">
              <?php foreach ($allUsersList as $u): ?>
                <option value="<?php echo (int)$u['idUser']; ?>" <?php echo ($isLoggedIn && ID_USER == $u['idUser']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($u['prenomUser'] . ' ' . $u['nomEUser']); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-sleep-primary py-1 px-2" style="font-size: 0.75rem;">
              Changer
            </button>
          </form>
        </div>
      <?php } ?>

      <!-- Conteneur Deck Tinder -->
      <div class="tinder-app-container">

        <!-- État vide -->
        <div id="deckEmptyState" class="tinder-empty-deck w-100">
          <div style="font-size: 3rem;" class="mb-2">😴💤</div>
          <h5 class="fw-bold text-white mb-2">Tous les siesteurs ont été vus</h5>
          <p class="text-secondary small mb-3">
            Vous avez passé en revue tous les profils disponibles pour le moment.
          </p>
          <button onclick="resetSleeperDeck()" class="btn btn-sleep-secondary btn-sm">
            <span>🔄 Revoir la sélection</span>
          </button>
        </div>

        <!-- Pile de cartes -->
        <div id="tinderDeck" class="tinder-deck-wrapper">
          <!-- Rendu interactif via JavaScript -->
        </div>

        <!-- Boutons d'action Tinder Flat : Rewind, Nope, Like -->
        <div id="tinderControls" class="tinder-action-bar">
          <button type="button" class="tinder-btn tinder-btn-small tinder-btn-rewind" onclick="rewindLastSwipe()" title="Annuler le dernier choix">
            ↺
          </button>
          <button type="button" class="tinder-btn tinder-btn-large tinder-btn-nope" onclick="handleSwipeBtn('pass')" title="Passer ce profil">
            ✕
          </button>
          <button type="button" class="tinder-btn tinder-btn-large tinder-btn-like" onclick="handleSwipeBtn('like')" title="Dormir ensemble (Like)">
            💤
          </button>
        </div>

      </div>

    </main>

  <?php } else { ?>

    <!-- =========================================================================
         DISPOSITION VISITEUR NON CONNECTÉ (Plein Écran Sobre)
         ========================================================================= -->
    <div class="w-100 h-100 d-flex flex-column" style="overflow-y: auto;">
      
      <!-- Barre Supérieure Visiteur -->
      <header class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom border-secondary border-opacity-10" style="background: var(--bg-sidebar);">
        <div class="sleepers-brand-logo">
          <span>🌙💤</span>
          <span>Sleepers</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="<?php echo ROOT_URL; ?>/views/backend/dashboard.php" class="btn btn-sm btn-link text-secondary text-decoration-none">
            Admin
          </a>
          <a href="<?php echo ROOT_URL; ?>/views/backend/security/login.php" class="btn btn-sm btn-sleep-secondary">
            Connexion
          </a>
          <a href="<?php echo ROOT_URL; ?>/views/backend/security/signup.php" class="btn btn-sm btn-sleep-primary">
            Créer un compte
          </a>
        </div>
      </header>

      <!-- Zone centrale de découverte -->
      <main class="flex-grow-1 d-flex flex-column align-items-center justify-content-center py-4 px-3">
        
        <?php if ($isTestBddEnabled) { ?>
          <div class="sleepers-test-bar mb-3">
            <span class="small text-secondary fw-semibold">🧪 Mode Démo :</span>
            <form action="<?php echo ROOT_URL; ?>/api/security/quick_switch.php" method="POST" class="d-inline-flex align-items-center gap-2 m-0">
              <select name="userId" aria-label="Choisir un profil de test">
                <?php foreach ($allUsersList as $u): ?>
                  <option value="<?php echo (int)$u['idUser']; ?>">
                    <?php echo htmlspecialchars($u['prenomUser'] . ' ' . $u['nomEUser']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="btn btn-sm btn-sleep-primary py-1 px-2" style="font-size: 0.75rem;">
                Tester
              </button>
            </form>
          </div>
        <?php } ?>

        <div class="tinder-app-container">
          <div id="deckEmptyState" class="tinder-empty-deck w-100">
            <div style="font-size: 3rem;" class="mb-2">😴💤</div>
            <h5 class="fw-bold text-white mb-2">Découvrez les siesteurs</h5>
            <p class="text-secondary small mb-3">
              Créez votre compte pour explorer l'ensemble des membres et convenir d'une sieste à deux.
            </p>
            <a href="<?php echo ROOT_URL; ?>/views/backend/security/signup.php" class="btn btn-sleep-primary btn-sm">
              Créer mon compte
            </a>
          </div>

          <div id="tinderDeck" class="tinder-deck-wrapper">
            <!-- Rendu dynamique -->
          </div>

          <div id="tinderControls" class="tinder-action-bar">
            <button type="button" class="tinder-btn tinder-btn-small tinder-btn-rewind" onclick="rewindLastSwipe()" title="Annuler">
              ↺
            </button>
            <button type="button" class="tinder-btn tinder-btn-large tinder-btn-nope" onclick="handleSwipeBtn('pass')" title="Passer">
              ✕
            </button>
            <button type="button" class="tinder-btn tinder-btn-large tinder-btn-like" onclick="handleSwipeBtn('like')" title="Like">
              💤
            </button>
          </div>
        </div>

        <!-- 3 Piliers de Sleepers (Uniquement pour visiteurs non connectés) -->
        <div class="row g-3 mt-4 w-100" style="max-width: 900px;">
          <div class="col-12 col-md-4">
            <div class="card p-3 h-100">
              <div class="fs-3 mb-1">🛏️</div>
              <div class="fw-bold text-white small mb-1">Compatibilité literie</div>
              <div class="text-secondary" style="font-size: 0.82rem;">Couette épaisse ou drap léger ? Trouvez un partenaire qui partage vos exigences de confort.</div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="card p-3 h-100">
              <div class="fs-3 mb-1">⏱️</div>
              <div class="fw-bold text-white small mb-1">Rythme synchronisé</div>
              <div class="text-secondary" style="font-size: 0.82rem;">Micro-sieste éclair de 20 minutes ou longue sieste de 2 heures le dimanche ? Matchez à votre rythme.</div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="card p-3 h-100">
              <div class="fs-3 mb-1">☕</div>
              <div class="fw-bold text-white small mb-1">Le réveil en douceur</div>
              <div class="text-secondary" style="font-size: 0.82rem;">Finis les réveils brutaux. Réveil calme, murmures et option café ou thé chaud au lit.</div>
            </div>
          </div>
        </div>

      </main>

    </div>

  <?php } ?>

</div>

<!-- =========================================================================
     MODALE : CONNEXION REQUISE (Visiteurs)
     ========================================================================= -->
<div id="authRequiredModal" class="sleepers-match-modal">
  <div class="sleepers-match-card">
    <div style="font-size: 2.75rem;" class="mb-2">🔒💤</div>
    <h4 class="fw-bold text-white mb-2">Connexion requise</h4>
    <p class="text-secondary small mb-4">
      Pour <span id="authActionText" class="text-white fw-semibold">interagir avec ce profil</span> et trouver votre partenaire de sieste, vous devez être connecté.
    </p>
    <div class="d-flex flex-column gap-2 mb-3">
      <a href="<?php echo ROOT_URL; ?>/views/backend/security/login.php" class="btn btn-sleep-primary py-2">
        🔑 Se connecter
      </a>
      <a href="<?php echo ROOT_URL; ?>/views/backend/security/signup.php" class="btn btn-sleep-secondary py-2">
        ✨ Créer un compte
      </a>
    </div>
    <button type="button" onclick="closeAuthModal()" class="btn btn-link text-secondary btn-sm text-decoration-none">
      Continuer à regarder
    </button>
  </div>
</div>

<!-- =========================================================================
     MODALE : C'EST UN MATCH !
     ========================================================================= -->
<div id="matchModal" class="sleepers-match-modal">
  <div class="sleepers-match-card">
    <div style="font-size: 2.5rem;" class="mb-2">✨💤</div>
    <h3 class="fw-bold text-white mb-2">C'est un Match !</h3>
    <p class="text-secondary small mb-3">
      Vous et <span id="matchPartnerName" class="fw-bold text-white"></span> avez envie de roupiller ensemble.
    </p>

    <div class="match-avatars">
      <img id="matchMyAvatar" src="<?php echo $currentUserPhoto ?: 'https://i.pravatar.cc/150?u=me'; ?>" class="match-avatar" alt="Moi" />
      <img id="matchPartnerAvatar" src="" class="match-avatar match-avatar-partner" alt="Partenaire" />
    </div>

    <div class="d-flex flex-column gap-2">
      <button type="button" onclick="closeMatchModal(); switchSideTab('matches'); toggleMobileView('sidebar');" class="btn btn-sleep-primary w-100 py-2">
        Voir mes matchs
      </button>
      <button type="button" onclick="closeMatchModal()" class="btn btn-sleep-secondary btn-sm w-100 py-2">
        Continuer à explorer
      </button>
    </div>
  </div>
</div>

<!-- =========================================================================
     LOGIQUE APPLICATIVE & INTERACTIONS
     ========================================================================= -->
<script>
  const IS_LOGGED_IN = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
  const CURRENT_USER_ID = <?php echo $isLoggedIn ? (int)ID_USER : 'null'; ?>;
  const RAW_PROFILES = <?php echo json_encode($dbProfiles, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Préparation des cartes pour le deck Tinder
  let deckProfiles = (RAW_PROFILES || []).map(p => {
    let photoUrl = '';
    const traits = p.sleep_traits || {};
    let bedDescription = traits.bed_type || '';

    if (p.photo && (p.photo.startsWith('http://') || p.photo.startsWith('https://'))) {
      photoUrl = p.photo;
    } else if (p.photo && p.photo.match(/\.(jpeg|jpg|gif|png|webp|svg)$/i)) {
      photoUrl = '<?php echo ROOT_URL; ?>/' + p.photo.replace(/^\/+/, '');
    } else {
      photoUrl = `https://i.pravatar.cc/500?u=sleepers_${p.idUser}`;
    }

    let habits = traits.sleep_habits || [];
    if (!habits || habits.length === 0) {
      habits = [
        '🛌 ' + (traits.pillow_count || 'Plaid douillet'),
        '⏱️ ' + (traits.nap_duration || '30 min'),
        traits.dreamer ? '💤 Rêveur' : '😴 Sommeil profond'
      ];
    }

    return {
      idUser: parseInt(p.idUser),
      prenomUser: p.prenomUser || 'Siesteur',
      nomEUser: p.nomEUser || '',
      age: p.age ? parseInt(p.age) : null,
      libGenr: p.libGenr || 'Sieste',
      photo: photoUrl,
      bedDescription: bedDescription,
      napDuration: traits.nap_duration || '',
      dreamer: traits.dreamer,
      sleepSound: traits.sleep_sound || '',
      biographie: p.biographie && p.biographie.trim() !== '' ? p.biographie : (traits.ideal_partner || 'Adepte des après-midis calmes et des siestes réparatrices.'),
      habits: habits
    };
  });

  let activeProfiles = [...deckProfiles];
  let swipeHistory = [];
  let currentMatchPartner = null;

  const tinderDeck = document.getElementById('tinderDeck');
  const deckEmptyState = document.getElementById('deckEmptyState');
  const tinderControls = document.getElementById('tinderControls');

  function renderDeck() {
    if (!tinderDeck) return;
    tinderDeck.innerHTML = '';

    if (activeProfiles.length === 0) {
      if (deckEmptyState) deckEmptyState.classList.add('show');
      if (tinderControls) {
        tinderControls.style.opacity = '0.35';
        tinderControls.style.pointerEvents = 'none';
      }
      return;
    }

    if (deckEmptyState) deckEmptyState.classList.remove('show');
    if (tinderControls) {
      tinderControls.style.opacity = '1';
      tinderControls.style.pointerEvents = 'auto';
    }

    activeProfiles.forEach((profile, index) => {
      const isTop = index === 0;
      const card = document.createElement('div');
      card.className = 'tinder-profile-card tinder-card-transition';
      card.id = `card-profile-${profile.idUser}`;

      const scale = 1 - index * 0.035;
      const translateY = index * 7;
      card.style.transform = `translateY(${translateY}px) scale(${scale})`;
      card.style.zIndex = 30 - index;

      const habitsHtml = (profile.habits || []).map(h => 
        `<span class="tinder-pill-habit">${escapeHtml(h)}</span>`
      ).join('');

      const ageText = profile.age ? `, <span class="tinder-profile-age">${profile.age} ans</span>` : '';
      const bedHtml = profile.bedDescription 
        ? `<div class="tinder-pill-bed">🛏️ ${escapeHtml(profile.bedDescription)}</div>` 
        : '';

      card.innerHTML = `
        <img src="${profile.photo}" alt="${escapeHtml(profile.prenomUser)}" class="tinder-card-photo" draggable="false" />
        <div class="tinder-card-overlay"></div>
        
        <div class="tinder-stamp tinder-stamp-like">DORMIR 💤</div>
        <div class="tinder-stamp tinder-stamp-nope">PASSER ✕</div>

        <div class="tinder-badge-top">
          <span class="tinder-tag">${escapeHtml(profile.libGenr || 'Sieste')}</span>
        </div>

        <div class="tinder-card-content">
          ${bedHtml}
          <div class="tinder-profile-name">
            ${escapeHtml(profile.prenomUser)}${profile.nomEUser ? ' ' + escapeHtml(profile.nomEUser) : ''}${ageText}
          </div>
          <div class="tinder-profile-bio">
            ${escapeHtml(profile.biographie)}
          </div>
          <div class="tinder-pill-habits">
            ${habitsHtml}
          </div>
        </div>
      `;

      if (isTop) {
        attachGestures(card, profile);
      }

      tinderDeck.appendChild(card);
    });
  }

  // Gestes tactiles & drag de la carte supérieure
  function attachGestures(cardEl, profile) {
    let startX = 0, startY = 0, currentX = 0, currentY = 0;
    let isDragging = false;
    const stampLike = cardEl.querySelector('.tinder-stamp-like');
    const stampNope = cardEl.querySelector('.tinder-stamp-nope');

    function onStart(e) {
      isDragging = true;
      startX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
      startY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
      cardEl.classList.remove('tinder-card-transition');
    }

    function onMove(e) {
      if (!isDragging) return;
      const x = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
      const y = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
      currentX = x - startX;
      currentY = y - startY;

      const rotate = currentX * 0.08;
      cardEl.style.transform = `translate(${currentX}px, ${currentY}px) rotate(${rotate}deg)`;

      if (currentX > 30) {
        if (stampLike) stampLike.style.opacity = Math.min(1, (currentX - 30) / 75);
        if (stampNope) stampNope.style.opacity = 0;
      } else if (currentX < -30) {
        if (stampNope) stampNope.style.opacity = Math.min(1, (-currentX - 30) / 75);
        if (stampLike) stampLike.style.opacity = 0;
      } else {
        if (stampLike) stampLike.style.opacity = 0;
        if (stampNope) stampNope.style.opacity = 0;
      }
    }

    function onEnd() {
      if (!isDragging) return;
      isDragging = false;
      cardEl.classList.add('tinder-card-transition');

      const threshold = 100;

      if (!IS_LOGGED_IN) {
        if (Math.abs(currentX) > threshold) {
          cardEl.style.transform = 'translate(0px, 0px) rotate(0deg)';
          if (stampLike) stampLike.style.opacity = 0;
          if (stampNope) stampNope.style.opacity = 0;
          showAuthModal(currentX > 0 ? 'liker' : 'passer');
          return;
        }
      }

      if (currentX > threshold) {
        executeSwipe('like', profile);
      } else if (currentX < -threshold) {
        executeSwipe('pass', profile);
      } else {
        cardEl.style.transform = 'translate(0px, 0px) rotate(0deg)';
        if (stampLike) stampLike.style.opacity = 0;
        if (stampNope) stampNope.style.opacity = 0;
      }
    }

    cardEl.addEventListener('mousedown', onStart);
    window.addEventListener('mousemove', onMove);
    window.addEventListener('mouseup', onEnd);

    cardEl.addEventListener('touchstart', onStart, { passive: true });
    window.addEventListener('touchmove', onMove, { passive: true });
    window.addEventListener('touchend', onEnd);
  }

  // Clic sur boutons de contrôle
  function handleSwipeBtn(action) {
    if (activeProfiles.length === 0) return;

    if (!IS_LOGGED_IN) {
      showAuthModal(action === 'like' ? 'liker' : 'passer');
      return;
    }

    const profile = activeProfiles[0];
    executeSwipe(action, profile);
  }

  function executeSwipe(action, profile) {
    const cardEl = document.getElementById(`card-profile-${profile.idUser}`);
    if (cardEl) {
      cardEl.classList.add('tinder-card-transition');
      const flyX = action === 'like' ? 600 : -600;
      const rotate = action === 'like' ? 22 : -22;
      cardEl.style.transform = `translate(${flyX}px, 0px) rotate(${rotate}deg)`;
      cardEl.style.opacity = '0';
    }

    sendSwipeToApi(profile.idUser, action, function(isMatch, partner) {
      if (isMatch) {
        showMatchModal(partner || profile);
        if (IS_LOGGED_IN) {
          loadMatchedUsers();
          loadLikedUsers();
        }
      } else {
        if (IS_LOGGED_IN && action === 'like') {
          loadLikedUsers();
        }
      }
    });

    setTimeout(() => {
      swipeHistory.push({ profile, action });
      activeProfiles.shift();
      renderDeck();
    }, 240);
  }

  function sendSwipeToApi(targetId, action, callback) {
    const formData = new FormData();
    formData.append('targetId', targetId);
    formData.append('action', action);

    fetch('<?php echo ROOT_URL; ?>/api/security/swipe.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success && callback) {
        callback(data.is_match, data.partner);
      }
    })
    .catch(err => console.error('Erreur swipe:', err));
  }

  function rewindLastSwipe() {
    if (swipeHistory.length === 0) {
      return;
    }
    const last = swipeHistory.pop();
    activeProfiles.unshift(last.profile);
    renderDeck();
  }

  function resetSleeperDeck() {
    activeProfiles = [...deckProfiles];
    swipeHistory = [];
    renderDeck();
  }

  // Modale connexion requise
  function showAuthModal(actionDesc) {
    const actionEl = document.getElementById('authActionText');
    if (actionEl && actionDesc) {
      actionEl.textContent = `${actionDesc} ce profil`;
    }
    document.getElementById('authRequiredModal').classList.add('show');
  }

  function closeAuthModal() {
    document.getElementById('authRequiredModal').classList.remove('show');
  }

  // Modale Match
  function showMatchModal(partner) {
    currentMatchPartner = partner;
    document.getElementById('matchPartnerName').textContent = partner.name || partner.prenomUser;
    document.getElementById('matchPartnerAvatar').src = partner.photo || `https://i.pravatar.cc/150?u=sleepers_${partner.id || partner.idUser}`;
    document.getElementById('matchModal').classList.add('show');
  }

  function closeMatchModal() {
    document.getElementById('matchModal').classList.remove('show');
  }

  // Bascule onglets sidebar
  function switchSideTab(tabName) {
    const tabMatches = document.getElementById('tabContentMatches');
    const tabLiked = document.getElementById('tabContentLiked');
    const btnMatches = document.getElementById('tabBtnMatches');
    const btnLiked = document.getElementById('tabBtnLiked');

    if (tabName === 'matches') {
      if (tabMatches) tabMatches.classList.remove('d-none');
      if (tabLiked) tabLiked.classList.add('d-none');
      if (btnMatches) btnMatches.classList.add('active');
      if (btnLiked) btnLiked.classList.remove('active');
      loadMatchedUsers();
    } else {
      if (tabMatches) tabMatches.classList.add('d-none');
      if (tabLiked) tabLiked.classList.remove('d-none');
      if (btnMatches) btnMatches.classList.remove('active');
      if (btnLiked) btnLiked.classList.add('active');
      loadLikedUsers();
    }
  }

  // Bascule vue mobile (Deck vs Sidebar)
  function toggleMobileView(view) {
    const sidebar = document.getElementById('sleepersSidebar');
    const btnDeck = document.getElementById('mobileBtnDeck');
    const btnMatches = document.getElementById('mobileBtnMatches');

    if (view === 'sidebar') {
      if (sidebar) sidebar.classList.add('mobile-open');
      if (btnMatches) { btnMatches.classList.add('btn-sleep-primary'); btnMatches.classList.remove('btn-outline-light'); }
      if (btnDeck) { btnDeck.classList.remove('btn-sleep-primary'); btnDeck.classList.add('btn-outline-light'); }
    } else {
      if (sidebar) sidebar.classList.remove('mobile-open');
      if (btnDeck) { btnDeck.classList.add('btn-sleep-primary'); btnDeck.classList.remove('btn-outline-light'); }
      if (btnMatches) { btnMatches.classList.remove('btn-sleep-primary'); btnMatches.classList.add('btn-outline-light'); }
    }
  }

  // 1. Charger et afficher les matchs
  function loadMatchedUsers() {
    if (!IS_LOGGED_IN) return;

    fetch('<?php echo ROOT_URL; ?>/api/security/matched_users.php')
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('matchesContainer');
      const badge = document.getElementById('badgeMatchesCount');

      let matches = data.matches || [];
      if (badge) badge.textContent = matches.length;

      if (!container) return;

      if (matches.length === 0) {
        container.innerHTML = `
          <div class="text-center py-5 text-secondary">
            <div class="fs-1 mb-2">😴💤</div>
            <div class="fw-semibold text-white mb-1">Aucun match pour l'instant</div>
            <div class="small">Likez les profils qui vous plaisent pour déclencher un match réciproque.</div>
          </div>
        `;
        return;
      }

      let html = '<div class="d-flex flex-column gap-2">';
      matches.forEach(m => {
        let commentsHtml = '';
        if (m.comments && m.comments.length > 0) {
          commentsHtml = m.comments.map(c => `
            <div class="sleepers-comment-bubble">
              <span class="fw-bold text-white small">${escapeHtml(c.authorName || 'Membre')} :</span>
              <span class="text-secondary small ms-1">${escapeHtml(c.libComment)}</span>
            </div>
          `).join('');
        } else {
          commentsHtml = '<div class="text-secondary small fst-italic mb-2">Aucun avis laissé pour le moment.</div>';
        }

        html += `
          <div class="sleepers-item-card" id="match-item-${m.id}">
            <div class="d-flex align-items-center gap-3 mb-2">
              <img src="${m.photo}" class="rounded-circle object-fit-cover border border-secondary" style="width: 46px; height: 46px;" alt="${escapeHtml(m.name)}" />
              <div class="flex-grow-1 min-w-0">
                <div class="d-flex justify-content-between align-items-baseline">
                  <span class="fw-bold text-white fs-6">${escapeHtml(m.name)}${m.age ? ', ' + m.age + ' ans' : ''}</span>
                  <span class="badge bg-success" style="font-size: 0.68rem;">Match</span>
                </div>
                <div class="small text-secondary text-truncate">🛏️ ${escapeHtml(m.bedType)}</div>
              </div>
            </div>

            <div class="mt-2 pt-2 border-top border-secondary border-opacity-25">
              <div class="small fw-semibold text-white mb-2">💬 Avis sur ${escapeHtml(m.prenomUser)} :</div>
              <div class="mb-2" id="comments-list-${m.id}">
                ${commentsHtml}
              </div>

              <form onsubmit="submitMatchComment(event, ${m.id})" class="d-flex gap-2 mt-2">
                <input type="text" name="comment" class="form-control form-control-sm" placeholder="Ajouter un avis sur ${escapeHtml(m.prenomUser)}..." maxlength="300" required />
                <button type="submit" class="btn btn-sm btn-sleep-primary px-3">Publier</button>
              </form>
            </div>
          </div>
        `;
      });
      html += '</div>';
      container.innerHTML = html;
    })
    .catch(err => console.error('Erreur chargement matchs:', err));
  }

  // 2. Publier un avis sur une personne matchée
  function submitMatchComment(event, targetId) {
    event.preventDefault();
    const form = event.target;
    const input = form.querySelector('input[name="comment"]');
    const commentText = input ? input.value.trim() : '';

    if (!commentText) return;

    const formData = new FormData();
    formData.append('targetId', targetId);
    formData.append('comment', commentText);

    fetch('<?php echo ROOT_URL; ?>/api/security/add_match_comment.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        input.value = '';
        loadMatchedUsers();
      } else {
        alert(data.error || 'Impossible d\'enregistrer le commentaire.');
      }
    })
    .catch(err => console.error('Erreur publication avis:', err));
  }

  // 3. Charger et afficher les profils likés
  function loadLikedUsers() {
    if (!IS_LOGGED_IN) return;

    fetch('<?php echo ROOT_URL; ?>/api/security/liked_users.php')
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('likedContainer');
      const badge = document.getElementById('badgeLikedCount');

      let liked = data.liked || [];
      if (badge) badge.textContent = liked.length;

      if (!container) return;

      if (liked.length === 0) {
        container.innerHTML = `
          <div class="text-center py-5 text-secondary">
            <div class="fs-1 mb-2">❤️</div>
            <div class="fw-semibold text-white mb-1">Aucun profil liké</div>
            <div class="small">Swipez vers la droite sur les profils pour leur envoyer un like.</div>
          </div>
        `;
        return;
      }

      let html = '<div class="d-flex flex-column gap-2">';
      liked.forEach(l => {
        html += `
          <div class="sleepers-item-card d-flex align-items-center justify-content-between gap-3" id="liked-item-${l.id}">
            <div class="d-flex align-items-center gap-3 min-w-0">
              <img src="${l.photo}" class="rounded-circle object-fit-cover border border-secondary" style="width: 44px; height: 44px;" alt="${escapeHtml(l.name)}" />
              <div class="min-w-0">
                <div class="fw-bold text-white fs-6 text-truncate">${escapeHtml(l.name)}${l.age ? ', ' + l.age + ' ans' : ''}</div>
                <div class="small text-secondary text-truncate">🛏️ ${escapeHtml(l.bedType)}</div>
                ${l.isMatched ? '<span class="badge bg-success" style="font-size: 0.65rem;">Match réciproque</span>' : '<span class="badge bg-secondary" style="font-size: 0.65rem;">En attente</span>'}
              </div>
            </div>
            <div>
              <button onclick="unlikeProfile(${l.id}, '${escapeHtml(l.name)}')" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 py-1 px-2" title="Retirer mon like">
                <span>💔 Unlike</span>
              </button>
            </div>
          </div>
        `;
      });
      html += '</div>';
      container.innerHTML = html;
    })
    .catch(err => console.error('Erreur chargement likes:', err));
  }

  // 4. Retirer un like (Unlike)
  function unlikeProfile(targetId, targetName) {
    if (!confirm(`Retirer votre like pour ${targetName} ?`)) return;

    const formData = new FormData();
    formData.append('targetId', targetId);

    fetch('<?php echo ROOT_URL; ?>/api/security/unlike.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        loadLikedUsers();
        loadMatchedUsers();
      } else {
        alert(data.error || 'Erreur lors du retrait du like.');
      }
    })
    .catch(err => console.error('Erreur unlike:', err));
  }

  // Initialisation
  renderDeck();
  if (IS_LOGGED_IN) {
    loadMatchedUsers();
    loadLikedUsers();
  }
</script>

<?php require_once 'footer.php'; ?>