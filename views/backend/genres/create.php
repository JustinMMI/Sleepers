<?php
include '../../../header.php';
require_once dirname(__DIR__) . '/admin_helpers.php';
?>

<main class="container py-4">
	<h1 class="mb-4">Créer un genre</h1>
	<form action="<?php echo admin_api_url('genres', 'create'); ?>" method="post" class="col-12 col-md-7">
		<div class="mb-3">
			<label for="libGenr" class="form-label">Libellé</label>
			<input id="libGenr" name="libGenr" class="form-control" type="text" maxlength="30" required autofocus="autofocus" />
		</div>
		<a href="list.php" class="btn btn-secondary">Annuler</a>
		<button type="submit" class="btn btn-success">Créer</button>
	</form>
</main>
<?php include '../../../footer.php'; ?>

