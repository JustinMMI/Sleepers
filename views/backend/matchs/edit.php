<?php
include '../../../header.php';
require_once dirname(__DIR__) . '/admin_helpers.php';
$idUserM1 = admin_query_id('idUserM1');
$idUserM2 = admin_query_id('idUserM2');
$matchs = sql_select('MATCHS', '*', 'idUserM1 = ' . $idUserM1 . ' AND idUserM2 = ' . $idUserM2);
if (!$matchs) { admin_redirect('/views/backend/matchs/list.php'); }
$users = admin_user_options();
?>

<main class="container py-4"><h1 class="mb-4">Modifier un match</h1><form action="<?php echo admin_api_url('matchs', 'update'); ?>" method="post" class="col-12 col-lg-8">
<input type="hidden" name="oldIdUserM1" value="<?php echo $idUserM1; ?>" /><input type="hidden" name="oldIdUserM2" value="<?php echo $idUserM2; ?>" />
<div class="mb-3"><label for="idUserM1" class="form-label">Utilisateur</label><select id="idUserM1" name="idUserM1" class="form-select" required><?php foreach ($users as $user) { ?><option value="<?php echo (int) $user['idUser']; ?>" <?php echo (int) $user['idUser'] === $idUserM1 ? 'selected' : ''; ?>><?php echo admin_escape(admin_user_label($user)); ?></option><?php } ?></select></div>
<div class="mb-3"><label for="idUserM2" class="form-label">Profil correspondant</label><select id="idUserM2" name="idUserM2" class="form-select" required><?php foreach ($users as $user) { ?><option value="<?php echo (int) $user['idUser']; ?>" <?php echo (int) $user['idUser'] === $idUserM2 ? 'selected' : ''; ?>><?php echo admin_escape(admin_user_label($user)); ?></option><?php } ?></select></div>
<a href="list.php" class="btn btn-secondary">Annuler</a> <button type="submit" class="btn btn-success">Enregistrer</button></form></main>
<?php include '../../../footer.php'; ?>

