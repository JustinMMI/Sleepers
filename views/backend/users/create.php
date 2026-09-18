<?php
include '../../../header.php';
require_once dirname(__DIR__) . '/admin_helpers.php';
$genres = admin_genre_options();
?>

<main class="container py-4">
	<h1 class="mb-4">Créer un utilisateur</h1>
	<form action="<?php echo admin_api_url('users', 'create'); ?>" method="post" class="col-12 col-lg-8">
		<div class="row">
			<div class="col-md-6 mb-3"><label for="nomEUser" class="form-label">Nom</label><input id="nomEUser" name="nomEUser" class="form-control" maxlength="50" required /></div>
			<div class="col-md-6 mb-3"><label for="prenomUser" class="form-label">Prénom</label><input id="prenomUser" name="prenomUser" class="form-control" maxlength="50" required /></div>
		</div>
		<div class="mb-3"><label for="emailUser" class="form-label">Email</label><input id="emailUser" name="emailUser" class="form-control" type="email" maxlength="255" required /></div>
		<div class="mb-3"><label for="passwordUser" class="form-label">Mot de passe</label><input id="passwordUser" name="passwordUser" class="form-control" type="password" minlength="8" required /></div>
		<div class="mb-3"><label for="idGenr" class="form-label">Genre</label><select id="idGenr" name="idGenr" class="form-select" required><option value="">Choisir</option><?php foreach ($genres as $genre) { ?><option value="<?php echo (int) $genre['idGenr']; ?>"><?php echo admin_escape($genre['libGenr']); ?></option><?php } ?></select></div>
		<div class="row">
			<div class="col-md-4 mb-3"><label for="age" class="form-label">Age</label><input id="age" name="age" class="form-control" type="number" min="1" max="120" /></div>
			<div class="col-md-8 mb-3"><label for="photo" class="form-label">Photo</label><input id="photo" name="photo" class="form-control" maxlength="50" /></div>
		</div>
		<div class="mb-3"><label for="biographie" class="form-label">Biographie</label><textarea id="biographie" name="biographie" class="form-control" maxlength="150" rows="3"></textarea></div>
		<a href="list.php" class="btn btn-secondary">Annuler</a>
		<button type="submit" class="btn btn-success">Créer</button>
	</form>
</main>
<?php include '../../../footer.php'; ?>

