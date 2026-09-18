<?php
include '../../../header.php'; // contains the header and call to config.php
require_once dirname(__DIR__) . '/admin_helpers.php';

$genres = sql_select('GENRE', '*', null, null, 'idGenr ASC');
?>

<main class="container py-4">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1>Genres</h1>
		<a href="create.php" class="btn btn-success">Créer un genre</a>
	</div>
	<table class="table table-striped align-middle">
		<thead><tr><th>ID</th><th>Libellé</th><th>Actions</th></tr></thead>
		<tbody>
		<?php foreach ($genres as $genre) { ?>
			<tr>
				<td><?php echo (int) $genre['idGenr']; ?></td>
				<td><?php echo admin_escape($genre['libGenr']); ?></td>
				<td>
					<a href="edit.php?idGenr=<?php echo (int) $genre['idGenr']; ?>" class="btn btn-warning btn-sm">Modifier</a>
					<a href="delete.php?idGenr=<?php echo (int) $genre['idGenr']; ?>" class="btn btn-danger btn-sm">Supprimer</a>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</main>
<?php include '../../../footer.php'; ?>

