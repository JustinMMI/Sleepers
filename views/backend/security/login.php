<?php
include '../../../header.php';

?>

<main class="container py-5">
	<div class="row justify-content-center">
		<div class="col-12 col-md-7 col-lg-5">
			<h1 class="mb-4">Connexion</h1>
			<form action="<?php echo ROOT_URL . '/api/security/login.php'; ?>" method="post">
				<div class="mb-3">
					<label for="emailUser" class="form-label">Adresse email</label>
					<input id="emailUser" name="emailUser" class="form-control" type="email" autocomplete="email" required autofocus="autofocus" />
				</div>
				<div class="mb-3">
					<label for="passwordUser" class="form-label">Mot de passe</label>
					<input id="passwordUser" name="passwordUser" class="form-control" type="password" autocomplete="current-password" required />
				</div>
				<button type="submit" class="btn btn-primary">Se connecter</button>
				<a href="signup.php" class="btn btn-link">Créer un compte</a>
			</form>
		</div>
	</div>
</main>

