<?php 
require_once 'header.php';
sql_connect();
global $DB;

$isLoggedIn = defined('ID_USER');
$currentUser = null;
$dbProfiles = array();
$allUsersList = array();

// Récupérer la liste complète des utilisateurs pour le sélecteur de test rapide
try {
    $allUsersStmt = $DB->query('SELECT idUser, prenomUser, nomEUser, age FROM `USER` ORDER BY idUser ASC');
    $allUsersList = $allUsersStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $allUsersList = array();
}

if ($isLoggedIn) {
    // Récupérer les informations du membre connecté
    $stmt = $DB->prepare('SELECT u.*, g.libGenr FROM `USER` u LEFT JOIN GENRE g ON u.idGenr = g.idGenr WHERE u.idUser = :id');
    $stmt->execute(array(':id' => ID_USER));
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

    // Profils non encore swipés par ce membre (exclut soi-même et les profils déjà dans LIKES)
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
    // VISITEUR NON CONNECTE : Récupérer TOUS les profils réels de la table USER
    $stmtProfiles = $DB->query(
        'SELECT u.idUser, u.nomEUser, u.prenomUser, u.age, u.photo, u.biographie, g.libGenr
         FROM `USER` u
         LEFT JOIN GENRE g ON u.idGenr = g.idGenr
         ORDER BY u.idUser ASC'
    );
    $dbProfiles = $stmtProfiles->fetchAll(PDO::FETCH_ASSOC);
}
?>

<main class="container py-3">

  <?php if ($isLoggedIn) { ?>
    <!-- =========================================================================
         BARRE MEMBRE CONNECTE
         ========================================================================= -->
    <div class="d-flex justify-content-between align-items-center max-w-md mx-auto mb-3" style="max-width: 480px;">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle overflow-hidden d-flex align-items-center justify-content-center border border-2 border-purple-400" style="width: 46px; height: 46px; background: #1f1b3c;">
          <?php 
            if (!empty($currentUser['photo']) && (str_starts_with($currentUser['photo'], 'http') || preg_match('/\.(jpg|jpeg|png|webp)$/i', $currentUser['photo']))) {
              echo '<img src="' . htmlspecialchars($currentUser['photo'], ENT_QUOTES) . '" class="w-100 h-100 object-fit-cover" />';
            } else {
              echo '<img src="https://i.pravatar.cc/150?u=sleepers_' . (int)$currentUser['idUser'] . '" class="w-100 h-100 object-fit-cover" />';
            }
          ?>
        </div>
        <div>
          <div class="small" style="color: #94a3b8; font-size: 0.8rem;">Connecté en tant que</div>
          <div class="fw-bold text-white fs-6"><?php echo htmlspecialchars(($currentUser['prenomUser'] ?? 'Siesteur') . ' ' . ($currentUser['nomEUser'] ?? ''), ENT_QUOTES); ?> <span class="badge" style="background: rgba(168, 85, 247, 0.35); border: 1px solid #a855f7; color: #f3e8ff; font-size: 0.72rem;">#<?php echo (int)$currentUser['idUser']; ?></span></div>
        </div>
      </div>
      <div>
        <button onclick="toggleMatchDrawer()" class="btn btn-sm btn-sleep-secondary rounded-pill px-3 py-1 d-flex align-items-center gap-2">
          <span>💤 Matchs</span>
          <span id="badgeMatchCount" class="badge bg-danger rounded-pill" style="font-size: 0.75rem;">0</span>
        </button>
      </div>
    </div>

  <?php } else { ?>
    <!-- =========================================================================
         BANNIERE DÉCOUVERTE POUR VISITEUR NON CONNECTE
         ========================================================================= -->
    <div class="max-w-md mx-auto mb-4" style="max-width: 480px;">
      <div class="sleepers-discovery-card">
        <div class="d-flex align-items-center gap-2 mb-2">
          <span class="fs-4">🌙💤</span>
          <h5 class="fw-bold text-white mb-0">Bienvenue sur Sleepers</h5>
        </div>
        <p class="mb-3" style="color: #cbd5e1; font-size: 0.95rem;">
          Le premier site de rencontre pour ne plus jamais faire la sieste seul(e). Découvrez les profils réels ci-dessous !
          <strong class="text-white d-block mt-1">Connectez-vous pour liker, matcher et convenir d'une sieste à deux.</strong>
        </p>
        <div class="d-flex gap-2">
          <a href="<?php echo ROOT_URL; ?>/views/backend/security/login.php" class="btn-sleep-primary flex-grow-1 text-center">
            <span>🔑 Se connecter</span>
          </a>
          <a href="<?php echo ROOT_URL; ?>/views/backend/security/signup.php" class="btn-sleep-secondary flex-grow-1 text-center">
            <span>✨ Inscription</span>
          </a>
        </div>
      </div>
    </div>
  <?php } ?>

  <!-- Switcher de test rapide (Haute lisibilité, zéro blanc sur blanc) -->
  <div class="d-flex justify-content-center mb-3">
    <div class="sleepers-test-bar">
      <span class="fw-semibold text-white small">🧪 Test BDD :</span>
      <form action="<?php echo ROOT_URL; ?>/api/security/quick_switch.php" method="POST" class="d-inline-flex align-items-center gap-2 m-0">
        <select name="userId" aria-label="Choisir un profil de test">
          <?php foreach ($allUsersList as $u): ?>
            <option value="<?php echo (int)$u['idUser']; ?>" <?php echo ($isLoggedIn && ID_USER == $u['idUser']) ? 'selected' : ''; ?>>
              #<?php echo (int)$u['idUser']; ?> - <?php echo htmlspecialchars($u['prenomUser'] . ' ' . $u['nomEUser']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm btn-sleep-primary py-1 px-3" style="font-size: 0.8rem;">
          <?php echo $isLoggedIn ? 'Changer' : 'Tester ce profil'; ?>
        </button>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       LE DECK DE CARTES TINDER (Présente tous les profils réels de la table USER)
       ========================================================================= -->
  <div class="tinder-app-container my-0">
    
    <!-- État vide (quand tous les profils de la BDD ont été vus) -->
    <div id="deckEmptyState" class="tinder-empty-deck w-100">
      <div style="font-size: 3.5rem;" class="mb-3">😴💤</div>
      <h4 class="fw-bold text-white mb-2">Tous les siesteurs de la BDD ont été vus !</h4>
      <p class="text-white-50 small mb-3">
        Il n'y a plus d'autre profil à afficher dans la table <code>USER</code>.
      </p>
      <div class="d-flex flex-wrap gap-2 justify-content-center">
        <a href="<?php echo ROOT_URL; ?>/views/backend/security/signup.php" class="btn btn-sleep-primary btn-sm">
          <span>➕ Inscrire un profil siesteur</span>
        </a>
        <button onclick="resetSleeperDeck()" class="btn btn-outline-light btn-sm">
          <span>🔄 Recharger la sélection</span>
        </button>
      </div>
    </div>

    <!-- Pile de cartes interactives -->
    <div id="tinderDeck" class="tinder-deck-wrapper">
      <!-- Rendu dynamique par JavaScript avec les données réelles de la table USER -->
    </div>

    <!-- Barre d'actions Tinder -->
    <div id="tinderControls" class="tinder-action-bar">
      <button type="button" class="tinder-btn tinder-btn-small tinder-btn-rewind" onclick="rewindLastSwipe()" title="Annuler le dernier choix">
        ↺
      </button>
      <button type="button" class="tinder-btn tinder-btn-large tinder-btn-nope" onclick="handleSwipeBtn('pass')" title="Passer (Pas pour cette sieste)">
        ✕
      </button>
      <button type="button" class="tinder-btn tinder-btn-small tinder-btn-super" onclick="handleSwipeBtn('super')" title="Super Sieste !">
        ⭐
      </button>
      <button type="button" class="tinder-btn tinder-btn-large tinder-btn-like" onclick="handleSwipeBtn('like')" title="Envie de dormir ensemble !">
        💤
      </button>
      <button type="button" class="tinder-btn tinder-btn-small tinder-btn-comment" onclick="handleCommentBtn()" title="Envoyer un mot doux">
        💬
      </button>
    </div>

  </div>

  <!-- =========================================================================
       3 PILIERS DE SLEEPERS
       ========================================================================= -->
  <div class="row g-4 mt-5 mb-5 pb-4">
    <div class="col-md-4">
      <div class="sleepers-feature-card">
        <div class="sleepers-feature-icon">🛏️</div>
        <h4 class="fw-bold mb-2">Compatibilité literie</h4>
        <p class="text-white-50 small mb-0">Plutôt couette en duvet ou drap léger ? 4 oreillers moelleux ou un seul plat ? Trouvez quelqu'un qui partage vos exigences de confort.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="sleepers-feature-card">
        <div class="sleepers-feature-icon">⏱️</div>
        <h4 class="fw-bold mb-2">Rythme synchronisé</h4>
        <p class="text-white-50 small mb-0">Micro-sieste de 20 minutes en début d'après-midi ou sieste marathon de 3 heures le dimanche ? Matchez selon la durée qui vous convient.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="sleepers-feature-card">
        <div class="sleepers-feature-icon">☕</div>
        <h4 class="fw-bold mb-2">Le réveil en douceur</h4>
        <p class="text-white-50 small mb-0">Finis les réveils brutaux. Nos membres s'engagent à se réveiller avec respect, murmures et option café ou thé chaud.</p>
      </div>
    </div>
  </div>

</main>

<!-- =========================================================================
     MODALE : CONNEXION REQUISE (Pour visiteurs non connectés)
     ========================================================================= -->
<div id="authRequiredModal" class="sleepers-match-modal">
  <div class="sleepers-match-card text-center">
    <div style="font-size: 3rem;" class="mb-2">🔒💤</div>
    <h3 class="fw-bold text-white mb-2">Connexion requise</h3>
    <p class="text-white-50 small mb-3">
      Pour <span id="authActionText" class="text-white fw-semibold">interagir avec ce siesteur</span> et trouver votre partenaire de sieste idéal, vous devez posséder un compte membre.
    </p>
    <div class="d-flex flex-column gap-2 mb-3">
      <a href="<?php echo ROOT_URL; ?>/views/backend/security/login.php" class="btn btn-sleep-primary py-2">
        <span>🔑 Se connecter</span>
      </a>
      <a href="<?php echo ROOT_URL; ?>/views/backend/security/signup.php" class="btn btn-sleep-secondary py-2">
        <span>✨ Créer mon compte (gratuit)</span>
      </a>
    </div>
    <button type="button" onclick="closeAuthModal()" class="btn btn-link text-white-50 btn-sm text-decoration-none">
      Continuer à regarder les profils
    </button>
  </div>
</div>

<!-- =========================================================================
     MODALE : C'EST UN MATCH ! (Lors d'un match réel en BDD)
     ========================================================================= -->
<div id="matchModal" class="sleepers-match-modal">
  <div class="sleepers-match-card">
    <div style="font-size: 2.5rem;" class="mb-2">🛏️✨</div>
    <h2 class="h3 fw-black text-uppercase sleepers-gradient-text mb-2">C'est un Match !</h2>
    <p class="text-white-50 small mb-3">
      Vous et <span id="matchPartnerName" class="fw-bold text-white"></span> avez envie de roupiller ensemble !
    </p>

    <div class="match-avatars">
      <img id="matchMyAvatar" src="<?php echo !empty($currentUser['photo']) && (str_starts_with($currentUser['photo'], 'http') || preg_match('/\.(jpg|jpeg|png|webp)$/i', $currentUser['photo'])) ? htmlspecialchars($currentUser['photo'], ENT_QUOTES) : 'https://i.pravatar.cc/150?u=sleepers_' . ($isLoggedIn ? (int)ID_USER : 'me'); ?>" class="match-avatar" />
      <img id="matchPartnerAvatar" src="" class="match-avatar match-avatar-partner" />
    </div>

    <div class="d-flex flex-column gap-2">
      <button type="button" onclick="openMatchChatFromModal()" class="btn btn-sleep-primary w-100 py-2">
        <span>💬 Proposer une sieste</span>
      </button>
      <button type="button" onclick="closeMatchModal()" class="btn btn-outline-light btn-sm w-100 py-2">
        💤 Continuer à explorer
      </button>
    </div>
  </div>
</div>

<!-- =========================================================================
     TIROIR : VOS MATCHS & DISCUSSIONS (Table MATCHS & COMMENTS)
     ========================================================================= -->
<div id="matchDrawer" class="sleepers-drawer" onclick="if(event.target===this) toggleMatchDrawer();">
  <div class="sleepers-drawer-content">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary">
      <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
        <span>💤</span> Vos Matchs de Sieste
      </h5>
      <button type="button" class="btn-close btn-close-white" onclick="toggleMatchDrawer()"></button>
    </div>
    
    <div id="matchesListContainer">
      <div class="text-center py-4 text-white-50 small">Chargement de vos matchs...</div>
    </div>
  </div>
</div>

<!-- =========================================================================
     SCRIPT INTERACTIF TINDER DE LA SIESTE
     ========================================================================= -->
<script>
  // État d'authentification
  const IS_LOGGED_IN = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
  const CURRENT_USER_ID = <?php echo $isLoggedIn ? (int)ID_USER : 'null'; ?>;

  // Profils réels issus de la table USER de la base TINDER22
  const RAW_DB_PROFILES = <?php echo json_encode($dbProfiles, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Traitement et préparation des profils réels de la BDD
  let deckProfiles = (RAW_DB_PROFILES || []).map(p => {
    let photoUrl = '';
    let bedDescription = '';

    if (p.photo && (p.photo.startsWith('http://') || p.photo.startsWith('https://'))) {
      photoUrl = p.photo;
    } else if (p.photo && p.photo.match(/\.(jpeg|jpg|gif|png|webp|svg)$/i)) {
      photoUrl = '<?php echo ROOT_URL; ?>/' + p.photo.replace(/^\/+/, '');
    } else {
      // Données textuelles spécifiques de la BDD (ex: 'LIT BLEU', 'Carapace très spacieuse', etc.)
      if (p.photo && p.photo.trim() !== '') {
        bedDescription = p.photo.trim();
      }
      photoUrl = `https://i.pravatar.cc/500?u=sleepers_${p.idUser}`;
    }

    return {
      idUser: parseInt(p.idUser),
      prenomUser: p.prenomUser || 'Siesteur',
      nomEUser: p.nomEUser || '',
      age: p.age ? parseInt(p.age) : null,
      libGenr: p.libGenr || 'Sieste',
      photo: photoUrl,
      bedDescription: bedDescription,
      biographie: p.biographie && p.biographie.trim() !== '' ? p.biographie : 'Adepte des après-midis calmes et des siestes réparatrices.',
      habits: ['🛌 Plaid douillet', '⏱️ 30 min', '💤 Rêveur']
    };
  });

  let activeProfiles = [...deckProfiles];
  let swipeHistory = [];
  let currentMatchPartner = null;

  const tinderDeck = document.getElementById('tinderDeck');
  const deckEmptyState = document.getElementById('deckEmptyState');
  const tinderControls = document.getElementById('tinderControls');

  function renderDeck() {
    tinderDeck.innerHTML = '';

    if (activeProfiles.length === 0) {
      deckEmptyState.classList.add('show');
      tinderControls.style.opacity = '0.4';
      tinderControls.style.pointerEvents = 'none';
      return;
    }

    deckEmptyState.classList.remove('show');
    tinderControls.style.opacity = '1';
    tinderControls.style.pointerEvents = 'auto';

    activeProfiles.forEach((profile, index) => {
      const isTop = index === 0;
      const card = document.createElement('div');
      card.className = 'tinder-profile-card tinder-card-transition';
      card.id = `card-profile-${profile.idUser}`;

      // Empilement visuel façon Tinder
      const scale = 1 - index * 0.04;
      const translateY = index * 8;
      card.style.transform = `translateY(${translateY}px) scale(${scale})`;
      card.style.zIndex = 30 - index;

      const habitsHtml = (profile.habits || ['🛌 Plaid douillet', '⏱️ 30 min', '💤 Rêveur']).map(h => 
        `<span class="tinder-pill-habit">${h}</span>`
      ).join('');

      const ageText = profile.age ? `, <span class="tinder-profile-age">${profile.age} ans</span>` : '';
      const bedHtml = profile.bedDescription 
        ? `<div class="tinder-pill-bed">🛏️ Literie : <strong>${escapeHtml(profile.bedDescription)}</strong></div>` 
        : '';

      card.innerHTML = `
        <img src="${profile.photo}" alt="${escapeHtml(profile.prenomUser)}" class="tinder-card-photo" draggable="false" />
        <div class="tinder-card-overlay"></div>
        
        <div class="tinder-stamp tinder-stamp-like">DORMIR 💤</div>
        <div class="tinder-stamp tinder-stamp-nope">PASSER ❌</div>

        <div class="tinder-badge-top">
          <span class="tinder-tag">ID #${profile.idUser}</span>
          <span class="tinder-tag tinder-tag-genre">${escapeHtml(profile.libGenr || 'Sieste')}</span>
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

  // Gestes tactiles et glisser-déposer de la carte supérieure
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
        if (stampLike) stampLike.style.opacity = Math.min(1, (currentX - 30) / 80);
        if (stampNope) stampNope.style.opacity = 0;
      } else if (currentX < -30) {
        if (stampNope) stampNope.style.opacity = Math.min(1, (-currentX - 30) / 80);
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

      const threshold = 110;

      // SI NON CONNECTE : l'utilisateur ne peut rien faire en BDD -> retour en place et modal de connexion
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

  // Clic sur les boutons de contrôle
  function handleSwipeBtn(action) {
    if (activeProfiles.length === 0) return;

    // Protection utilisateur non connecté
    if (!IS_LOGGED_IN) {
      showAuthModal(action === 'like' ? 'liker' : (action === 'pass' ? 'passer' : 'envoyer une super-sieste'));
      return;
    }

    const profile = activeProfiles[0];
    executeSwipe(action, profile);
  }

  function handleCommentBtn() {
    if (!IS_LOGGED_IN) {
      showAuthModal('envoyer un mot doux');
      return;
    }
    promptQuickComment();
  }

  // Animation et enregistrement du Swipe en BDD
  function executeSwipe(action, profile) {
    const cardEl = document.getElementById(`card-profile-${profile.idUser}`);
    if (cardEl) {
      cardEl.classList.add('tinder-card-transition');
      const flyX = action === 'like' ? 650 : (action === 'pass' ? -650 : 0);
      const flyY = action === 'super' ? -650 : 0;
      const rotate = action === 'like' ? 25 : (action === 'pass' ? -25 : 0);
      cardEl.style.transform = `translate(${flyX}px, ${flyY}px) rotate(${rotate}deg)`;
      cardEl.style.opacity = '0';
    }

    // Appel API réel qui insère directement dans la table LIKES (et MATCHS si réciproque)
    sendSwipeToApi(profile.idUser, action, function(isMatch, partner) {
      if (isMatch) {
        showMatchModal(partner || profile);
        loadMatches();
      }
    });

    setTimeout(() => {
      swipeHistory.push({ profile, action });
      activeProfiles.shift();
      renderDeck();
    }, 260);
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
    .catch(err => console.error('Erreur API swipe:', err));
  }

  function rewindLastSwipe() {
    if (swipeHistory.length === 0) {
      alert("Aucun swipe précédent à annuler.");
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

  // Modale d'authentification requise
  function showAuthModal(actionDesc) {
    const actionEl = document.getElementById('authActionText');
    if (actionEl && actionDesc) {
      actionEl.textContent = `${actionDesc} ce siesteur`;
    }
    document.getElementById('authRequiredModal').classList.add('show');
  }

  function closeAuthModal() {
    document.getElementById('authRequiredModal').classList.remove('show');
  }

  // Modale C'est un Match (déclenchée lors d'un vrai match réciproque en BDD)
  function showMatchModal(partner) {
    currentMatchPartner = partner;
    document.getElementById('matchPartnerName').textContent = partner.name || partner.prenomUser;
    document.getElementById('matchPartnerAvatar').src = partner.photo || `https://i.pravatar.cc/150?u=sleepers_${partner.id || partner.idUser}`;
    document.getElementById('matchModal').classList.add('show');
  }

  function closeMatchModal() {
    document.getElementById('matchModal').classList.remove('show');
  }

  function openMatchChatFromModal() {
    closeMatchModal();
    if (currentMatchPartner) {
      promptQuickComment(currentMatchPartner);
    }
  }

  // Envoi d'un message doux stocké dans la table COMMENTS
  function promptQuickComment(targetPartner) {
    const partner = targetPartner || (activeProfiles.length > 0 ? activeProfiles[0] : currentMatchPartner);
    if (!partner) return;

    const partnerName = partner.name || partner.prenomUser;
    const partnerId = partner.id || partner.idUser;

    const message = prompt(`Envoyer un mot doux (enregistré dans la table COMMENTS) à ${partnerName} :`, "Coucou, partante pour une sieste au calme ce week-end ? 🛏️");
    if (message && message.trim() !== '') {
      const formData = new FormData();
      formData.append('targetId', partnerId);
      formData.append('message', message.trim());

      fetch('<?php echo ROOT_URL; ?>/api/security/comment.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        alert(`Message enregistré en BDD dans COMMENTS pour ${partnerName} : « ${data.message} » 💌`);
        loadMatches();
      })
      .catch(err => {
        console.error('Erreur API comment:', err);
      });
    }
  }

  // Gestion du tiroir des matchs
  function toggleMatchDrawer() {
    if (!IS_LOGGED_IN) {
      showAuthModal('voir vos matchs');
      return;
    }
    const drawer = document.getElementById('matchDrawer');
    drawer.classList.toggle('show');
    if (drawer.classList.contains('show')) {
      loadMatches();
    }
  }

  // Récupération des matchs réels depuis la table MATCHS
  function loadMatches() {
    if (!IS_LOGGED_IN) return;

    fetch('<?php echo ROOT_URL; ?>/api/security/matches.php')
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('matchesListContainer');
      const badge = document.getElementById('badgeMatchCount');

      let matches = data.matches || [];
      if (badge) badge.textContent = matches.length;

      if (matches.length === 0) {
        container.innerHTML = '<div class="text-center py-4 text-white-50 small">Aucun match dans la table <code>MATCHS</code> pour le moment.<br>Likez un profil qui vous like en retour pour créer un match réel ! 💤</div>';
        return;
      }

      let html = '<div class="d-flex flex-column gap-2">';
      matches.forEach(m => {
        const photoSrc = m.photo && (m.photo.startsWith('http') || m.photo.match(/\.(jpg|jpeg|png|webp)$/i)) 
          ? m.photo 
          : `https://i.pravatar.cc/150?u=sleepers_${m.id}`;

        html += `
          <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
            <img src="${photoSrc}" class="rounded-circle object-fit-cover" style="width: 48px; height: 48px;" />
            <div class="flex-grow-1 min-w-0">
              <div class="d-flex justify-content-between align-items-baseline">
                <span class="fw-bold text-white small">${escapeHtml(m.name)} (#${m.id})</span>
                <span class="text-purple-300" style="font-size: 0.7rem;">Match BDD</span>
              </div>
              <div class="text-white-50 text-truncate small">${escapeHtml(m.lastComment || 'Match enregistré en BDD ! Envoyez un mot doux.')}</div>
            </div>
            <button onclick="promptQuickComment({id: ${m.id}, name: '${escapeHtml(m.name)}'})" class="btn btn-sm btn-outline-light rounded-circle" style="width: 32px; height: 32px; padding: 0;">💬</button>
          </div>
        `;
      });
      html += '</div>';
      container.innerHTML = html;
    })
    .catch(err => console.error('Erreur API matches:', err));
  }

  // Initialisation au chargement de la page
  renderDeck();
  if (IS_LOGGED_IN) {
    loadMatches();
  }
</script>

<?php require_once 'footer.php'; ?>