<?php
include '../../../header.php'; // contains the header and call to config.php
require_once dirname(__DIR__) . '/admin_helpers.php';
$likes = sql_select('LIKES', '*', null, null, 'idUserL1 ASC, idUserL2 ASC');
?>

<main class="container py-4"><div class="d-flex justify-content-between align-items-center mb-4"><h1>Likes</h1><a href="create.php" class="btn btn-success">Créer un like</a></div>
<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Utilisateur</th><th>Profil ciblé</th><th>Valeur</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($likes as $like) { ?><tr><td><?php echo (int) $like['idUserL1']; ?></td><td><?php echo (int) $like['idUserL2']; ?></td><td><?php echo (int) $like['likeL1'] === 1 ? 'Oui' : 'Non'; ?></td><td><a href="edit.php?idUserL1=<?php echo (int) $like['idUserL1']; ?>&idUserL2=<?php echo (int) $like['idUserL2']; ?>" class="btn btn-warning btn-sm">Modifier</a> <a href="delete.php?idUserL1=<?php echo (int) $like['idUserL1']; ?>&idUserL2=<?php echo (int) $like['idUserL2']; ?>" class="btn btn-danger btn-sm">Supprimer</a></td></tr><?php } ?>
</tbody></table></div></main>
<?php include '../../../footer.php'; ?>

