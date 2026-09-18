<?php
include '../../../header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-5">
            <h1 class="mb-4">Accès administrateur</h1>
            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger" role="alert">Mot de passe incorrect.</div>
            <?php } ?>
            <form action="<?php echo ROOT_URL . '/api/security/admin-login.php'; ?>" method="post">
                <div class="mb-3">
                    <label for="adminPassword" class="form-label">Mot de passe administrateur</label>
                    <input id="adminPassword" name="adminPassword" class="form-control" type="password" autocomplete="current-password" required autofocus="autofocus" />
                </div>
                <button type="submit" class="btn btn-primary">Accéder au panneau</button>
                <a href="<?php echo ROOT_URL; ?>/" class="btn btn-link">Retour au site</a>
            </form>
        </div>
    </div>
</main>
