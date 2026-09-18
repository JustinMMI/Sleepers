<?php
include '../../../header.php';
require_once dirname(__DIR__) . '/admin_helpers.php';
$idUserL1 = admin_query_id('idUserL1');
$idUserL2 = admin_query_id('idUserL2');
$likes = sql_select('LIKES', '*', 'idUserL1 = ' . $idUserL1 . ' AND idUserL2 = ' . $idUserL2);
if (!$likes) { admin_redirect('/views/backend/likes/list.php'); }
$like = $likes[0];
?>

<main class="container py-4"><h1 class="mb-4">Modifier un like</h1><form action="<?php echo admin_api_url('likes', 'update'); ?>" method="post" class="col-12 col-md-7">
<input type="hidden" name="idUserL1" value="<?php echo $idUserL1; ?>" /><input type="hidden" name="idUserL2" value="<?php echo $idUserL2; ?>" />
<div class="mb-3"><label for="likeL1" class="form-label">Like</label><select id="likeL1" name="likeL1" class="form-select" required><option value="1" <?php echo (int) $like['likeL1'] === 1 ? 'selected' : ''; ?>>Oui</option><option value="0" <?php echo (int) $like['likeL1'] === 0 ? 'selected' : ''; ?>>Non</option></select></div>
<a href="list.php" class="btn btn-secondary">Annuler</a> <button type="submit" class="btn btn-success">Enregistrer</button></form></main>
<?php include '../../../footer.php'; ?>

