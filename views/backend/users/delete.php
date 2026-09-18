<?php
include '../../../header.php';
require_once dirname(__DIR__) . '/admin_helpers.php';
$idUser = admin_required_id('idUser');
$users = sql_select('`USER`', 'idUser, nomEUser, prenomUser, emailUser', 'idUser = ' . $idUser);
if (!$users) {
	admin_redirect('/views/backend/users/list.php');
}
$user = $users[0];
?>

<main class="container py-4">
	<h1 class="mb-4">Supprimer un utilisateur</h1>
	<p>Voulez-vous supprimer « <?php echo admin_escape(admin_user_label($user)); ?> » ?</p>
	<form action="<?php echo admin_api_url('users', 'delete'); ?>" method="post">
		<input type="hidden" name="idUser" value="<?php echo $idUser; ?>" />
		<a href="list.php" class="btn btn-secondary">Annuler</a>
		<button type="submit" class="btn btn-danger">Supprimer</button>
	</form>
</main>
<?php include '../../../footer.php'; ?>

