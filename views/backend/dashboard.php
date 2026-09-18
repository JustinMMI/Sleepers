<?php
include '../../header.php';
sql_connect();
global $DB;

$isTestBdd = function_exists('is_test_bdd_enabled') ? is_test_bdd_enabled() : false;

// Statistiques en direct de la plateforme
$totalUsers = 0;
$totalLikes = 0;
$totalMatches = 0;
$totalComments = 0;

try {
    $totalUsers = (int) $DB->query('SELECT COUNT(*) FROM `USER`')->fetchColumn();
    $totalLikes = (int) $DB->query('SELECT COUNT(*) FROM LIKES WHERE likeL1 = 1')->fetchColumn();
    $totalMatches = (int) $DB->query('SELECT COUNT(*) FROM MATCHS')->fetchColumn();
    $totalComments = (int) $DB->query('SELECT COUNT(*) FROM COMMENTS')->fetchColumn();
} catch (Exception $e) {
    // Silencieux si table inaccessible
}
?>

<!-- Barre supérieure de navigation Admin -->
<header class="d-flex justify-content-between align-items-center py-3 px-4 border-bottom border-secondary border-opacity-10" style="background: var(--bg-sidebar);">
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo ROOT_URL; ?>/" class="sleepers-brand-logo text-decoration-none">
            <span>🌙💤</span>
            <span>Sleepers</span>
        </a>
        <span class="badge bg-secondary ms-2" style="font-size: 0.72rem;">Administration</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo ROOT_URL; ?>/" class="btn btn-sm btn-outline-light">← Retour au site</a>
        <a href="<?php echo ROOT_URL; ?>/api/security/admin-logout.php" class="btn btn-sm btn-outline-warning">Quitter l'admin</a>
    </div>
</header>

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1">Panneau d'administration</h2>
            <p class="text-secondary mb-0">Bienvenue sur le dashboard de gestion Sleepers</p>
        </div>
    </div>

    <!-- Cartes Statistiques en direct -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1px solid var(--border-subtle);">
                <div class="text-secondary small mb-1">Membres inscrits</div>
                <div class="fs-3 fw-bold text-white"><?php echo $totalUsers; ?></div>
                <div class="small text-secondary mt-1">Profils dans la base</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1px solid var(--border-subtle);">
                <div class="text-secondary small mb-1">Likes émis</div>
                <div class="fs-3 fw-bold text-white"><?php echo $totalLikes; ?></div>
                <div class="small text-secondary mt-1">Swipes d'intérêt</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1px solid var(--border-subtle);">
                <div class="text-secondary small mb-1">Matchs réciproques</div>
                <div class="fs-3 fw-bold text-white"><?php echo $totalMatches; ?></div>
                <div class="small text-secondary mt-1">Siestes convenues</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card p-3 h-100" style="background: var(--bg-surface); border: 1px solid var(--border-subtle);">
                <div class="text-secondary small mb-1">Avis certifiés</div>
                <div class="fs-3 fw-bold text-white"><?php echo $totalComments; ?></div>
                <div class="small text-secondary mt-1">Retours d'expérience</div>
            </div>
        </div>
    </div>

    <!-- Section Options Sleepers (Mode Démo) -->
    <div class="card p-4 shadow-sm mb-4" style="background: var(--bg-surface); border: 1px solid var(--border-subtle);">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <h4 class="fw-bold text-white mb-1">⚙️ Configuration de la plateforme</h4>
                <p class="text-secondary small mb-0">
                    Paramètres applicatifs enregistrés de façon permanente.
                </p>
            </div>
            <?php if (isset($_GET['saved'])) { ?>
                <div>
                    <span class="badge bg-success py-2 px-3">✓ Paramètre enregistré</span>
                </div>
            <?php } ?>
        </div>

        <div class="p-3 rounded-3" style="background: rgba(0, 0, 0, 0.2); border: 1px solid var(--border-subtle);">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <div class="fw-bold text-white">Sélecteur de comptes "Mode Démo" (Page d'accueil)</div>
                    <div class="small text-secondary mt-1">
                        Permet de tester la plateforme en switchant en un clic entre les profils pour explorer les swipes et matchs réciproques.
                    </div>
                    <div class="small mt-2">
                        <span class="text-secondary">État actuel :</span>
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
                        <button type="submit" class="btn btn-sm <?php echo $isTestBdd ? 'btn-outline-danger' : 'btn-sleep-primary'; ?>">
                            <?php echo $isTestBdd ? 'Désactiver le sélecteur' : 'Activer le sélecteur'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestion des données de la plateforme -->
    <div class="card p-4 shadow-sm mb-4" style="background: var(--bg-surface); border: 1px solid var(--border-subtle);">
        <h4 class="fw-bold text-white mb-3">🗄️ Gestion des Données</h4>
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Objets</th>
                        <th>Actions</th>
                        <th>Description</th>
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
                        <td class="text-secondary small">Comptes et fiches des membres</td>
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
                        <td class="text-secondary small">Genres définis dans l'application</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Commentaires &amp; Avis</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo ROOT_URL; ?>/views/backend/comments/list.php" class="btn btn-primary">List</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/comments/create.php" class="btn btn-success">Create</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/comments/list.php" class="btn btn-warning">Edit</a>
                                <a href="<?php echo ROOT_URL; ?>/views/backend/comments/list.php" class="btn btn-danger">Delete</a>
                            </div>
                        </td>
                        <td class="text-secondary small">Avis laissés exclusivement sur les personnes matchées</td>
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
                        <td class="text-secondary small">Swipes et marques d'intérêt</td>
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
                        <td class="text-secondary small">Matchs réciproques confirmés</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../../footer.php'; ?>