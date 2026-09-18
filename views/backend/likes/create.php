<?php
include '../../../header.php';
require_once dirname(__DIR__) . '/admin_helpers.php';
$users = admin_user_options();
?>

<main class="container py-4"><h1 class="mb-4">Créer un like</h1><form action="<?php echo admin_api_url('likes', 'create'); ?>" method="post" class="col-12 col-lg-8">
<div class="mb-3"><label for="idUserL1" class="form-label">Utilisateur</label><select id="idUserL1" name="idUserL1" class="form-select" required><option value="">Choisir</option><?php foreach ($users as $user) { ?><option value="<?php echo (int) $user['idUser']; ?>"><?php echo admin_escape(admin_user_label($user)); ?></option><?php } ?></select></div>
<div class="mb-3"><label for="idUserL2" class="form-label">Profil ciblé</label><select id="idUserL2" name="idUserL2" class="form-select" required><option value="">Choisir</option><?php foreach ($users as $user) { ?><option value="<?php echo (int) $user['idUser']; ?>"><?php echo admin_escape(admin_user_label($user)); ?></option><?php } ?></select></div>
<div class="mb-3"><label for="likeL1" class="form-label">Like</label><select id="likeL1" name="likeL1" class="form-select" required><option value="1">Oui</option><option value="0">Non</option></select></div>
<a href="list.php" class="btn btn-secondary">Annuler</a> <button type="submit" class="btn btn-success">Créer</button></form></main>
<?php include '../../../footer.php'; ?>

