<?php
include '../../../header.php'; // contains the header and call to config.php
require_once dirname(__DIR__) . '/admin_helpers.php';

$users = sql_select('`USER`', 'idUser, idGenr, nomEUser, prenomUser, emailUser, photo, age, biographie', null, null, 'idUser ASC');
$genreLabels = admin_genre_labels(array_column($users, 'idGenr'));
?>

<main class="container py-4">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1>Utilisateurs</h1>
		<a href="create.php" class="btn btn-success">Créer un utilisateur</a>
	</div>
	<div class="table-responsive">
		<table class="table table-striped align-middle">
			<thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Genre</th><th>Age</th><th>Actions</th></tr></thead>
			<tbody>
			<?php foreach ($users as $user) { ?>
				<tr>
					<td><?php echo (int) $user['idUser']; ?></td>
					<td><?php echo admin_escape(trim($user['prenomUser'] . ' ' . $user['nomEUser'])); ?></td>
					<td><?php echo admin_escape($user['emailUser']); ?></td>
					<td><?php echo admin_escape($genreLabels[(int) $user['idGenr']]); ?></td>
					<td><?php echo $user['age'] === null ? '-' : (int) $user['age']; ?></td>
					<td>
						<a href="edit.php?idUser=<?php echo (int) $user['idUser']; ?>" class="btn btn-warning btn-sm">Modifier</a>
						<a href="delete.php?idUser=<?php echo (int) $user['idUser']; ?>" class="btn btn-danger btn-sm">Supprimer</a>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</main>
<?php include '../../../footer.php'; ?>

