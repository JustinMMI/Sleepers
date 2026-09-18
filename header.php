<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tinder</title>
    <!-- Load CSS -->
    <link rel="stylesheet" href="<?php echo ROOT_URL; ?>/src/css/style.css" />
    <!-- Bootstrap CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo ROOT_URL; ?>/src/images/article1.png" />
</head>
<body>
<nav class="navbar navbar-expand-lg bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Tinder</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="/">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo ROOT_URL; ?>/views/backend/dashboard.php">Admin</a>
        </li>
      </ul>
    </div>
    <!--right align-->
    <div class="d-flex">
      <form class="d-flex" role="search">
          <input class="form-control me-2" type="search" placeholder="Rechercher sur le site…" aria-label="Search" >
      </form>
      <?php if (function_exists('is_admin_authenticated') && is_admin_authenticated()) { ?>
        <a class="btn btn-outline-secondary m-1" href="<?php echo ROOT_URL; ?>/api/security/admin-logout.php" role="button">Quitter l'admin</a>
      <?php } elseif (defined('ID_USER')) { ?>
        <a class="btn btn-outline-secondary m-1" href="<?php echo ROOT_URL; ?>/api/security/disconnect.php" role="button">Déconnexion</a>
      <?php } else { ?>
        <a class="btn btn-primary m-1" href="<?php echo ROOT_URL; ?>/views/backend/security/login.php" role="button">Connexion</a>
        <a class="btn btn-dark m-1" href="<?php echo ROOT_URL; ?>/views/backend/security/signup.php" role="button">Créer un compte</a>
      <?php } ?>
    </div>
  </div>
</nav>