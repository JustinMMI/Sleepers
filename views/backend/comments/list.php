<?php
include '../../../header.php'; // contains the header and call to config.php
require_once dirname(__DIR__) . '/admin_helpers.php';
$comments = sql_select('COMMENTS', '*', null, null, 'idUserC1 ASC, idUserC2 ASC');
$userLabels = admin_user_labels(array_merge(array_column($comments, 'idUserC1'), array_column($comments, 'idUserC2')));
?>

<main class="container py-4">
	<div class="d-flex justify-content-between align-items-center mb-4"><h1>Commentaires</h1><a href="create.php" class="btn btn-success">Créer un commentaire</a></div>
	<div class="table-responsive"><table class="table table-striped align-middle">
		<thead><tr><th>Auteur</th><th>Destinataire</th><th>Commentaire</th><th>Actions</th></tr></thead>
		<tbody><?php foreach ($comments as $comment) { ?><tr>
			<td><?php echo admin_escape($userLabels[(int) $comment['idUserC1']]); ?></td><td><?php echo admin_escape($userLabels[(int) $comment['idUserC2']]); ?></td><td><?php echo admin_escape($comment['libComment']); ?></td>
			<td><a href="edit.php?idUserC1=<?php echo (int) $comment['idUserC1']; ?>&idUserC2=<?php echo (int) $comment['idUserC2']; ?>" class="btn btn-warning btn-sm">Modifier</a> <a href="delete.php?idUserC1=<?php echo (int) $comment['idUserC1']; ?>&idUserC2=<?php echo (int) $comment['idUserC2']; ?>" class="btn btn-danger btn-sm">Supprimer</a></td>
		</tr><?php } ?></tbody>
	</table></div>
</main>
<?php include '../../../footer.php'; ?>

