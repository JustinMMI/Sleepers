<?php
include '../../../header.php';

$genres = sql_select('GENRE', 'idGenr, libGenr', null, null, 'libGenr ASC');
?>

<main class="container py-5">
	<div class="row justify-content-center">
		<div class="col-12 col-md-9 col-lg-7">
			<div class="card p-4 p-md-5 shadow-lg">
				<div class="text-center mb-4">
					<div class="fs-1 mb-2">✨🛌</div>
					<h2 class="fw-bold text-white mb-1">Rejoindre Sleepers</h2>
					<p class="text-white-50 small mb-0">Créez votre profil pour trouver votre partenaire de sieste idéal</p>
				</div>
				<?php if (!$genres) { ?>
					<div class="alert alert-warning" role="alert">Aucun genre n'est disponible pour créer un compte.</div>
				<?php } else { ?>
					<form action="<?php echo ROOT_URL . '/api/security/signup.php'; ?>" method="post">
						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="nomEUser" class="form-label">Nom</label>
								<input id="nomEUser" name="nomEUser" class="form-control" type="text" maxlength="50" autocomplete="family-name" placeholder="Dupont" required />
							</div>
							<div class="col-md-6 mb-3">
								<label for="prenomUser" class="form-label">Prénom</label>
								<input id="prenomUser" name="prenomUser" class="form-control" type="text" maxlength="50" autocomplete="given-name" placeholder="Alex" required />
							</div>
						</div>
						<div class="mb-3">
							<label for="emailUser" class="form-label">Adresse email</label>
							<input id="emailUser" name="emailUser" class="form-control" type="email" maxlength="255" autocomplete="email" placeholder="alex@exemple.fr" required autofocus="autofocus" />
						</div>
						<div class="mb-3">
							<label for="passwordUser" class="form-label">Mot de passe</label>
							<input id="passwordUser" name="passwordUser" class="form-control" type="password" minlength="8" autocomplete="new-password" placeholder="8 caractères minimum" required />
						</div>
						<div class="mb-4">
							<label for="idGenr" class="form-label">Genre</label>
							<select id="idGenr" name="idGenr" class="form-select" required>
								<option value="">Choisir un genre</option>
								<?php foreach ($genres as $genre) { ?>
									<option value="<?php echo (int) $genre['idGenr']; ?>"><?php echo htmlspecialchars($genre['libGenr'], ENT_QUOTES, 'UTF-8'); ?></option>
								<?php } ?>
							</select>
						</div>
						<button type="submit" class="btn btn-sleep-primary w-100 py-2 mb-3">Créer mon compte</button>
						<div class="text-center">
							<span class="text-white-50 small">Déjà inscrit ?</span>
							<a href="login.php" class="btn btn-link py-0 fw-semibold">Se connecter</a>
						</div>
					</form>
				<?php } ?>
			</div>
		</div>
	</div>
</main>

<?php include '../../../footer.php'; ?>
