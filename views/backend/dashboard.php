<?php
include '../../header.php';

$isTestBdd = function_exists('is_test_bdd_enabled') ? is_test_bdd_enabled() : false;
?>

<!-- Bootstrap admin dashboard template -->
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="fw-bold text-white mb-1">Panneau d'administration</h1>
            <p class="text-white-50 mb-0">Bienvenue sur le dashboard de gestion Sleepers</p>
        </div>
        <div>
            <a href="<?php echo ROOT_URL; ?>/" class="btn btn-outline-light btn-sm">← Retour au site</a>
        </div>
    </div>

    <!-- Section Options Sleepers & Paramètres JSON -->
    <div class="card p-4 shadow-sm mb-4" style="border: 1px solid var(--sleep-border-purple); background: var(--sleep-bg-card);">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <h4 class="fw-bold text-white mb-1">⚙️ Configuration Sleepers</h4>
                <p class="text-white-50 small mb-0">
                    Options du site enregistrées dans le fichier JSON (sans modifier la BDD SQL).
                </p>
            </div>
            <?php if (isset($_GET['saved'])) { ?>
                <div>
                    <span class="badge bg-success py-2 px-3">✓ Paramètre enregistré avec succès</span>
                </div>
            <?php } ?>
        </div>

        <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1);">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <div class="fw-bold text-white">Sélecteur de comptes "Test BDD" (Page d'accueil)</div>
                    <div class="small text-white-50 mt-1">
                        Permet de switcher en 1 clic entre les profils de la BDD pour tester les swipes et matchs.
                    </div>
                    <div class="small mt-2">
                        <span class="text-white-50">État actuel :</span>
                        <?php if ($isTestBdd) { ?>
                            <span class="badge bg-success ms-1">✓ Activé</span>
                        <?php } else { ?>
                            <span class="badge bg-secondary ms-1">Désactivé par défaut</span>
                        <?php } ?>
                    </div>
                </div>
                <div>
                    <form action="<?php echo ROOT_URL; ?>/api/admin/toggle_test_mode.php" method="POST" class="m-0">
                        <input type="hidden" name="enable" value="<?php echo $isTestBdd ? '0' : '1'; ?>">
                        <button type="submit" class="btn <?php echo $isTestBdd ? 'btn-outline-danger' : 'btn-sleep-primary'; ?>">
                            <?php echo $isTestBdd ? 'Désactiver le sélecteur de test' : 'Activer le sélecteur de test'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestion des tables de la BDD -->
    <div class="card p-4 shadow-sm mb-4" style="background: var(--sleep-bg-card);">
        <h4 class="fw-bold text-white mb-3">🗄️ Gestion des Données BDD</h4>
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Objets</th>
                        <th>Actions CRUD</th>
                        <th>Commentaires</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-semibold">Users</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo ROOT_URL; ?>/views/backend/users/list.php" class="btn btn-primary">List</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/users/create.php" class="btn btn-success">Create</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/users/list.php" class="btn btn-warning">Edit</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/users/list.php" class="btn btn-danger">Delete</a>
                            </div>
                        </td>
                        <td class="text-white-50 small">Comptes utilisateurs de la table USER</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Genres</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo ROOT_URL; ?>/views/backend/genres/list.php" class="btn btn-primary">List</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/genres/create.php" class="btn btn-success">Create</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/genres/list.php" class="btn btn-warning">Edit</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/genres/list.php" class="btn btn-danger">Delete</a>
                            </div>
                        </td>
                        <td class="text-white-50 small">Genres (Femme, Homme)</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Commentaires</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo ROOT_URL; ?>/views/backend/comments/list.php" class="btn btn-primary">List</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/comments/create.php" class="btn btn-success">Create</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/comments/list.php" class="btn btn-warning">Edit</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/comments/list.php" class="btn btn-danger">Delete</a>
                            </div>
                        </td>
                        <td class="text-white-50 small">Mots doux échangés entre partenaires</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Likes</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo ROOT_URL; ?>/views/backend/likes/list.php" class="btn btn-primary">List</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/likes/create.php" class="btn btn-success">Create</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/likes/list.php" class="btn btn-warning">Edit</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/likes/list.php" class="btn btn-danger">Delete</a>
                            </div>
                        </td>
                        <td class="text-white-50 small">Swipes enregistrés dans LIKES</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Matchs</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo ROOT_URL; ?>/views/backend/matchs/list.php" class="btn btn-primary">List</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/matchs/create.php" class="btn btn-success">Create</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/matchs/list.php" class="btn btn-warning">Edit</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/matchs/list.php" class="btn btn-danger">Delete</a>
                            </div>
                        </td>
                        <td class="text-white-50 small">Matchs réciproques confirmés</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../../footer.php'; ?>