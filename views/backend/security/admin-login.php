<?php
include '../../../header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-5">
            <div class="card p-4 p-md-5 shadow-lg">
                <div class="text-center mb-4">
                    <div class="fs-1 mb-2">🔐🌙</div>
                    <h2 class="fw-bold text-white mb-1">Accès Administrateur</h2>
                    <p class="text-white-50 small mb-0">Panneau de gestion réservé aux administrateurs</p>
                </div>
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger" role="alert">Mot de passe administrateur incorrect.</div>
                <?php } ?>
                <form action="<?php echo ROOT_URL . '/api/security/admin-login.php'; ?>" method="post">
                    <div class="mb-4">
                        <label for="adminPassword" class="form-label">Mot de passe administrateur</label>
                        <input id="adminPassword" name="adminPassword" class="form-control" type="password" autocomplete="current-password" placeholder="••••••••" required autofocus="autofocus" />
                    </div>
                    <button type="submit" class="btn btn-sleep-primary w-100 py-2 mb-3">Accéder au panneau</button>
                    <div class="text-center">
                        <a href="<?php echo ROOT_URL; ?>/" class="btn btn-link py-0 fw-semibold">Retour au site</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include '../../../footer.php'; ?>
