<?php
include '../../../header.php';
require_once dirname(__DIR__) . '/admin_helpers.php';
$idUserC1 = admin_query_id('idUserC1');
$idUserC2 = admin_query_id('idUserC2');
$comments = sql_select('COMMENTS', '*', 'idUserC1 = ' . $idUserC1 . ' AND idUserC2 = ' . $idUserC2);
if (!$comments) { admin_redirect('/views/backend/comments/list.php'); }
$comment = $comments[0];
?>

<main class="container py-4"><h1 class="mb-4">Modifier un commentaire</h1>
<form action="<?php echo admin_api_url('comments', 'update'); ?>" method="post" class="col-12 col-lg-8">
	<input type="hidden" name="idUserC1" value="<?php echo $idUserC1; ?>" /><input type="hidden" name="idUserC2" value="<?php echo $idUserC2; ?>" />
	<div class="mb-3"><label for="libComment" class="form-label">Commentaire</label><textarea id="libComment" name="libComment" class="form-control" maxlength="300" required rows="4"><?php echo admin_escape($comment['libComment']); ?></textarea></div>
	<a href="list.php" class="btn btn-secondary">Annuler</a> <button type="submit" class="btn btn-success">Enregistrer</button>
</form></main>
<?php include '../../../footer.php'; ?>

