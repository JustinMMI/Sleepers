<?php
include '../../../header.php'; // contains the header and call to config.php
require_once dirname(__DIR__) . '/admin_helpers.php';
$matchs = sql_select('MATCHS', '*', null, null, 'idUserM1 ASC, idUserM2 ASC');
$userLabels = admin_user_labels(array_merge(array_column($matchs, 'idUserM1'), array_column($matchs, 'idUserM2')));
?>

<main class="container py-4"><div class="d-flex justify-content-between align-items-center mb-4"><h1>Matchs</h1><a href="create.php" class="btn btn-success">Créer un match</a></div>
<table class="table table-striped"><thead><tr><th>Utilisateur</th><th>Profil correspondant</th><th>Actions</th></tr></thead><tbody><?php foreach ($matchs as $match) { ?><tr><td><?php echo admin_escape($userLabels[(int) $match['idUserM1']]); ?></td><td><?php echo admin_escape($userLabels[(int) $match['idUserM2']]); ?></td><td><a href="edit.php?idUserM1=<?php echo (int) $match['idUserM1']; ?>&idUserM2=<?php echo (int) $match['idUserM2']; ?>" class="btn btn-warning btn-sm">Modifier</a> <a href="delete.php?idUserM1=<?php echo (int) $match['idUserM1']; ?>&idUserM2=<?php echo (int) $match['idUserM2']; ?>" class="btn btn-danger btn-sm">Supprimer</a></td></tr><?php } ?></tbody></table></main>
<?php include '../../../footer.php'; ?>

