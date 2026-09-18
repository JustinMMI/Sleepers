<?php
include '../../../header.php';
require_once dirname(__DIR__) . '/admin_helpers.php';
$users = admin_user_options();
?>

<main class="container py-4"><h1 class="mb-4">Créer un commentaire</h1>
<form action="<?php echo admin_api_url('comments', 'create'); ?>" method="post" class="col-12 col-lg-8">
	<div class="mb-3"><label for="idUserC1" class="form-label">Auteur</label><select id="idUserC1" name="idUserC1" class="form-select" required><option value="">Choisir</option><?php foreach ($users as $user) { ?><option value="<?php echo (int) $user['idUser']; ?>"><?php echo admin_escape(admin_user_label($user)); ?></option><?php } ?></select></div>
	<div class="mb-3"><label for="idUserC2" class="form-label">Destinataire</label><select id="idUserC2" name="idUserC2" class="form-select" required><option value="">Choisir</option><?php foreach ($users as $user) { ?><option value="<?php echo (int) $user['idUser']; ?>"><?php echo admin_escape(admin_user_label($user)); ?></option><?php } ?></select></div>
	<div class="mb-3"><label for="libComment" class="form-label">Commentaire</label><textarea id="libComment" name="libComment" class="form-control" maxlength="300" required rows="4"></textarea></div>
	<a href="list.php" class="btn btn-secondary">Annuler</a> <button type="submit" class="btn btn-success">Créer</button>
</form></main>
<?php include '../../../footer.php'; ?>

