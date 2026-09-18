<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sleepers - Le Tinder de la sieste</title>
    <!-- Bootstrap CSS (Loaded first) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <!-- Google Fonts for crisp modern typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Sleepers Custom CSS (Loaded after Bootstrap for proper cascading overrides) -->
    <link rel="stylesheet" href="<?php echo ROOT_URL; ?>/src/css/style.css" />
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo ROOT_URL; ?>/src/images/article1.png" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sleepers-navbar sticky-top">
  <div class="container">
    <a class="navbar-brand sleepers-brand" href="<?php echo ROOT_URL; ?>/">
      <span>🌙💤 Sleepers</span>
      <span class="sleepers-brand-badge">Sieste à deux</span>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo ROOT_URL; ?>/">Découverte</a>
        </li>
        <?php if (defined('ID_USER')) { ?>
          <li class="nav-item">
            <a class="nav-link" href="#" id="navMatchesLink" onclick="if(typeof toggleMatchDrawer === 'function'){toggleMatchDrawer(); return false;}">💤 Mes Matchs</a>
          </li>
        <?php } ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo ROOT_URL; ?>/views/backend/dashboard.php">Admin</a>
        </li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <?php if (function_exists('is_admin_authenticated') && is_admin_authenticated()) { ?>
          <a class="btn btn-outline-warning btn-sm" href="<?php echo ROOT_URL; ?>/api/security/admin-logout.php" role="button">Quitter l'admin</a>
        <?php } ?>
        <?php if (defined('ID_USER')) { ?>
          <a class="btn btn-outline-light btn-sm" href="<?php echo ROOT_URL; ?>/api/security/disconnect.php" role="button">Déconnexion</a>
        <?php } else { ?>
          <a class="btn btn-outline-light btn-sm" href="<?php echo ROOT_URL; ?>/views/backend/security/login.php" role="button">Connexion</a>
          <a class="btn btn-sleep-primary btn-sm px-3 py-1" href="<?php echo ROOT_URL; ?>/views/backend/security/signup.php" role="button">Créer un compte</a>
        <?php } ?>
      </div>
    </div>
  </div>
</nav>