<?php
include '../../../header.php';
require_once dirname(__DIR__) . '/admin_helpers.php';

$idGenr = admin_required_id('idGenr');
$genres = sql_select('GENRE', '*', 'idGenr = ' . $idGenr);
if (!$genres) {
	admin_redirect('/views/backend/genres/list.php');
}
$genre = $genres[0];
?>

<main class="container py-4">
	<h1 class="mb-4">Supprimer un genre</h1>
	<p>Voulez-vous supprimer le genre « <?php echo admin_escape($genre['libGenr']); ?> » ?</p>
	<form action="<?php echo admin_api_url('genres', 'delete'); ?>" method="post">
		<input type="hidden" name="idGenr" value="<?php echo $idGenr; ?>" />
		<a href="list.php" class="btn btn-secondary">Annuler</a>
		<button type="submit" class="btn btn-danger">Supprimer</button>
	</form>
</main>
<?php include '../../../footer.php'; ?>

