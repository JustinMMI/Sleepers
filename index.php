<?php 
require_once 'header.php';
sql_connect();
global $DB;

$isLoggedIn = defined('ID_USER');
$currentUser = null;
$dbProfiles = array();

if ($isLoggedIn) {
    // Récupérer les infos de l'utilisateur connecté
    $stmt = $DB->prepare('SELECT u.*, g.libGenr FROM `USER` u LEFT JOIN GENRE g ON u.idGenr = g.idGenr WHERE u.idUser = :id');
    $stmt->execute(array(':id' => ID_USER));
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupérer les profils non encore swipés par cet utilisateur
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
}
?>

<?php if (!$isLoggedIn) { ?>
  <!-- =========================================================================
       SECTION VISITEUR NON CONNECTE (Accès réservé / Landing de présentation)
       ========================================================================= -->
  <div class="container">
    <div class="sleepers-hero-section">
      <div class="sleepers-pill-tag">
        <span>✨</span> Le premier site de rencontre spécialisé dans la sieste
      </div>
      <h1 class="sleepers-hero-title">
        Ne faites plus jamais la sieste <span class="sleepers-gradient-text">en solo.</span>
      </h1>
      <p class="sleepers-hero-subtitle">
        Parce que dormir seul un dimanche après-midi sous un plaid géant, c'est bien dommage. 
        Trouvez votre partenaire de roupillon idéal, swipez selon vos affinités de sommeil et réveillez-vous heureux.
      </p>
      <div class="sleepers-cta-group">
        <a href="<?php echo ROOT_URL; ?>/views/backend/security/signup.php" class="btn-sleep-primary">
          <span>💤 Créer mon compte siesteur</span>
        </a>
        <a href="<?php echo ROOT_URL; ?>/views/backend/security/login.php" class="btn-sleep-secondary">
          <span>Se connecter</span>
        </a>
      </div>
    </div>

    <!-- Mockup Visuel de la Carte Tinder (Non interactif pour les visiteurs) -->
    <div class="row justify-content-center mb-5">
      <div class="col-12 col-md-8 col-lg-5">
        <div class="tinder-app-container my-0">
          <div class="tinder-deck-wrapper" style="height: 440px;">
            <div class="tinder-profile-card">
              <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=600&auto=format&fit=crop&q=80" alt="Camille" class="tinder-card-photo" />
              <div class="tinder-card-overlay"></div>
              <div class="tinder-badge-top">
                <span class="tinder-tag">✨ Top Siesteuse</span>
                <span class="tinder-tag tinder-tag-genre">Femme</span>
              </div>
              <div class="tinder-card-content">
                <div class="tinder-profile-name">
                  Camille, <span class="tinder-profile-age">24</span>
                </div>
                <div class="tinder-profile-bio">
                  « Adepte du plaid polaire et du bruit de la pluie. Je cherche quelqu'un de calme pour une sieste synchronisée de 45 min. »
                </div>
                <div class="tinder-pill-habits">
                  <span class="tinder-pill-habit">🛌 4 Oreillers</span>
                  <span class="tinder-pill-habit">⏱️ 45 min</span>
                  <span class="tinder-pill-habit">☕ Tisane camomille</span>
                </div>
              </div>
              <!-- Lock Overlay -->
              <div class="position-absolute inset-0 d-flex flex-column align-items-center justify-content-center" style="background: rgba(11, 14, 27, 0.75); backdrop-filter: blur(4px); z-index: 20;">
                <div style="font-size: 2.5rem;" class="mb-2">🔒</div>
                <h5 class="fw-bold text-white mb-2">Connectez-vous pour swiper</h5>
                <p class="text-white-50 small mb-3 text-center px-4">L'accès aux profils de siesteurs et aux interactions nécessite un compte.</p>
                <div class="d-flex gap-2">
                  <a href="<?php echo ROOT_URL; ?>/views/backend/security/login.php" class="btn btn-sm btn-outline-light px-3">Connexion</a>
                  <a href="<?php echo ROOT_URL; ?>/views/backend/security/signup.php" class="btn btn-sm btn-sleep-primary px-3">Inscription</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 3 Piliers de Sleepers -->
    <div class="row g-4 mb-5 pb-4">
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
  </div>

<?php } else { ?>
  <!-- =========================================================================
       SECTION UTILISATEUR CONNECTE (Interface Interactive Tinder de la Sieste)
       ========================================================================= -->
  <div class="container py-3">
    
    <!-- Header d'accueil du membre -->
    <div class="d-flex justify-content-between align-items-center max-w-md mx-auto mb-3" style="max-width: 440px;">
      <div class="d-flex align-items-center gap-2">
        <div class="rounded-circle overflow-hidden d-flex align-items-center justify-content-center bg-purple-900 border border-purple-400" style="width: 42px; height: 42px; font-size: 1.2rem;">
          <?php echo !empty($currentUser['photo']) ? '<img src="' . htmlspecialchars($currentUser['photo'], ENT_QUOTES) . '" class="w-100 h-100 object-fit-cover" />' : '😴'; ?>
        </div>
        <div>
          <div class="small text-white-50">Bonjour,</div>
          <div class="fw-bold text-white"><?php echo htmlspecialchars($currentUser['prenomUser'] ?? 'Siesteur', ENT_QUOTES); ?></div>
        </div>
      </div>
      <div>
        <button onclick="toggleMatchDrawer()" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1 d-flex align-items-center gap-1 border-purple-400">
          <span>💤 Matchs</span>
          <span id="badgeMatchCount" class="badge bg-danger rounded-pill" style="font-size: 0.75rem;">0</span>
        </button>
      </div>
    </div>

    <!-- Le Deck de Cartes Tinder -->
    <div class="tinder-app-container">
      
      <!-- État vide (quand tous les profils réels ont été swipés) -->
      <div id="deckEmptyState" class="tinder-empty-deck w-100">
        <div style="font-size: 3.5rem;" class="mb-3">😴💤</div>
        <h4 class="fw-bold text-white mb-2">Tous les siesteurs de la BDD ont été vus !</h4>
        <p class="text-white-50 small mb-3">
          Il n'y a plus d'autre profil à swiper dans la table <code>USER</code>.
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

      <!-- Pile de cartes -->
      <div id="tinderDeck" class="tinder-deck-wrapper">
        <!-- Rendu dynamique par JavaScript -->
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
        <button type="button" class="tinder-btn tinder-btn-small tinder-btn-comment" onclick="promptQuickComment()" title="Envoyer un mot doux">
          💬
        </button>
      </div>

    </div>
  </div>

  <!-- Fenêtre Modale festive : C'EST UN MATCH ! -->
  <div id="matchModal" class="sleepers-match-modal">
    <div class="sleepers-match-card">
      <div style="font-size: 2.5rem;" class="mb-2">🛏️✨</div>
      <h2 class="h3 fw-black text-uppercase sleepers-gradient-text mb-2">C'est un Match !</h2>
      <p class="text-white-50 small mb-3">
        Vous et <span id="matchPartnerName" class="fw-bold text-white"></span> avez envie de roupiller ensemble !
      </p>

      <div class="match-avatars">
        <img id="matchMyAvatar" src="<?php echo !empty($currentUser['photo']) ? htmlspecialchars($currentUser['photo'], ENT_QUOTES) : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150&auto=format&fit=crop&q=80'; ?>" class="match-avatar" />
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

  <!-- Tiroir / Drawer des Matchs & Discussions -->
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

  <!-- Script JS d'interaction Tinder -->
  <script>
    // Profils réels issus de la table USER de la base TINDER22
    const RAW_DB_PROFILES = <?php echo json_encode($dbProfiles, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    
    // Uniquement les profils de la base de données
    let deckProfiles = (RAW_DB_PROFILES || []).map(p => {
      let photoUrl = `https://i.pravatar.cc/300?u=${p.idUser}`;
      if (p.photo && p.photo.trim() !== '') {
        if (p.photo.startsWith('http://') || p.photo.startsWith('https://')) {
          photoUrl = p.photo;
        } else {
          photoUrl = '<?php echo ROOT_URL; ?>/' + p.photo.replace(/^\/+/, '');
        }
      }

      return {
        idUser: parseInt(p.idUser),
        prenomUser: p.prenomUser || 'Siesteur',
        nomEUser: p.nomEUser || '',
        age: p.age ? parseInt(p.age) : null,
        libGenr: p.libGenr || 'Sieste',
        photo: photoUrl,
        biographie: p.biographie && p.biographie.trim() !== '' ? p.biographie : 'Membre inscrit dans la base TINDER22.',
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

        // Visual stack depth
        const scale = 1 - index * 0.04;
        const translateY = index * 8;
        card.style.transform = `translateY(${translateY}px) scale(${scale})`;
        card.style.zIndex = 30 - index;

        const habitsHtml = (profile.habits || ['🛌 Plaid douillet', '⏱️ 30 min', '💤 Rêveur']).map(h => 
          `<span class="tinder-pill-habit">${h}</span>`
        ).join('');

        const ageText = profile.age ? `, <span class="tinder-profile-age">${profile.age}</span>` : '';

        card.innerHTML = `
          <img src="${profile.photo}" alt="${profile.prenomUser}" class="tinder-card-photo" draggable="false" />
          <div class="tinder-card-overlay"></div>
          
          <div class="tinder-stamp tinder-stamp-like">DORMIR 💤</div>
          <div class="tinder-stamp tinder-stamp-nope">PASSER ❌</div>

          <div class="tinder-badge-top">
            <span class="tinder-tag">ID BDD #${profile.idUser}</span>
            <span class="tinder-tag tinder-tag-genre">${profile.libGenr || 'Sieste'}</span>
          </div>

          <div class="tinder-card-content">
            <div class="tinder-profile-name">
              ${profile.prenomUser}${profile.nomEUser ? ' ' + profile.nomEUser : ''}${ageText}
            </div>
            <div class="tinder-profile-bio">
              ${profile.biographie || 'Prêt(e) pour une sieste au calme.'}
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

    // Gestures (Touch and Drag)
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
          stampLike.style.opacity = Math.min(1, (currentX - 30) / 80);
          stampNope.style.opacity = 0;
        } else if (currentX < -30) {
          stampNope.style.opacity = Math.min(1, (-currentX - 30) / 80);
          stampLike.style.opacity = 0;
        } else {
          stampLike.style.opacity = 0;
          stampNope.style.opacity = 0;
        }
      }

      function onEnd() {
        if (!isDragging) return;
        isDragging = false;
        cardEl.classList.add('tinder-card-transition');

        const threshold = 110;
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

    function handleSwipeBtn(action) {
      if (activeProfiles.length === 0) return;
      const profile = activeProfiles[0];
      executeSwipe(action, profile);
    }

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

    // Modal C'est un Match (déclenchée uniquement lors d'un vrai match BDD)
    function showMatchModal(partner) {
      currentMatchPartner = partner;
      document.getElementById('matchPartnerName').textContent = partner.name || partner.prenomUser;
      document.getElementById('matchPartnerAvatar').src = partner.photo || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=600&auto=format&fit=crop&q=80';
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
      const drawer = document.getElementById('matchDrawer');
      drawer.classList.toggle('show');
      if (drawer.classList.contains('show')) {
        loadMatches();
      }
    }

    // Récupération stricte des matchs depuis la table MATCHS
    function loadMatches() {
      fetch('<?php echo ROOT_URL; ?>/api/security/matches.php')
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById('matchesListContainer');
        const badge = document.getElementById('badgeMatchCount');

        let matches = data.matches || [];
        badge.textContent = matches.length;

        if (matches.length === 0) {
          container.innerHTML = '<div class="text-center py-4 text-white-50 small">Aucun match dans la table <code>MATCHS</code> pour le moment.<br>Likez un profil qui vous like en retour pour créer un match réel ! 💤</div>';
          return;
        }

        let html = '<div class="d-flex flex-column gap-2">';
        matches.forEach(m => {
          html += `
            <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
              <img src="${m.photo || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'}" class="rounded-circle object-fit-cover" style="width: 48px; height: 48px;" />
              <div class="flex-grow-1 min-w-0">
                <div class="d-flex justify-content-between align-items-baseline">
                  <span class="fw-bold text-white small">${m.name} (#${m.id})</span>
                  <span class="text-purple-300" style="font-size: 0.7rem;">Match BDD</span>
                </div>
                <div class="text-white-50 text-truncate small">${m.lastComment || 'Match enregistré en BDD ! Envoyez un mot doux.'}</div>
              </div>
              <button onclick="promptQuickComment({id: ${m.id}, name: '${m.name}'})" class="btn btn-sm btn-outline-light rounded-circle" style="width: 32px; height: 32px; padding: 0;">💬</button>
            </div>
          `;
        });
        html += '</div>';
        container.innerHTML = html;
      })
      .catch(err => console.error('Erreur API matches:', err));
    // Initialisation
    renderDeck();
    loadMatches();
  </script>
<?php } ?>

<?php require_once 'footer.php'; ?>