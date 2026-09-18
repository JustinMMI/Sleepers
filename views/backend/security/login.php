<?php
include '../../../header.php';
?>

<main class="container py-5">
	<div class="row justify-content-center">
		<div class="col-12 col-md-7 col-lg-5">
			<div class="card p-4 p-md-5 shadow-lg">
				<div class="text-center mb-4">
					<div class="fs-1 mb-2">🌙💤</div>
					<h2 class="fw-bold text-white mb-1">Connexion</h2>
					<p class="text-white-50 small mb-0">Retrouvez vos siesteurs favoris et vos matchs</p>
				</div>
				<form action="<?php echo ROOT_URL . '/api/security/login.php'; ?>" method="post">
					<div class="mb-3">
						<label for="emailUser" class="form-label">Adresse email</label>
						<input id="emailUser" name="emailUser" class="form-control" type="email" autocomplete="email" placeholder="nom@exemple.fr" required autofocus="autofocus" />
					</div>
					<div class="mb-4">
						<label for="passwordUser" class="form-label">Mot de passe</label>
						<input id="passwordUser" name="passwordUser" class="form-control" type="password" autocomplete="current-password" placeholder="••••••••" required />
					</div>
					<button type="submit" class="btn btn-sleep-primary w-100 py-2 mb-3">Se connecter</button>
					<div class="text-center">
						<span class="text-white-50 small">Pas encore de compte ?</span>
						<a href="signup.php" class="btn btn-link py-0 fw-semibold">Créer un compte</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</main>

<?php include '../../../footer.php'; ?>
