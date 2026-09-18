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
	<h1 class="mb-4">Modifier un genre</h1>
	<form action="<?php echo admin_api_url('genres', 'update'); ?>" method="post" class="col-12 col-md-7">
		<input type="hidden" name="idGenr" value="<?php echo $idGenr; ?>" />
		<div class="mb-3">
			<label for="libGenr" class="form-label">Libellé</label>
			<input id="libGenr" name="libGenr" class="form-control" type="text" maxlength="30" value="<?php echo admin_escape($genre['libGenr']); ?>" required autofocus="autofocus" />
		</div>
		<a href="list.php" class="btn btn-secondary">Annuler</a>
		<button type="submit" class="btn btn-success">Enregistrer</button>
	</form>
</main>
<?php include '../../../footer.php'; ?>

