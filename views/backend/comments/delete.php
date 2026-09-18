<?php
include '../../../header.php';
require_once dirname(__DIR__) . '/admin_helpers.php';
$idUserC1 = admin_query_id('idUserC1');
$idUserC2 = admin_query_id('idUserC2');
$comments = sql_select('COMMENTS', '*', 'idUserC1 = ' . $idUserC1 . ' AND idUserC2 = ' . $idUserC2);
if (!$comments) { admin_redirect('/views/backend/comments/list.php'); }
$comment = $comments[0];
?>

<main class="container py-4"><h1 class="mb-4">Supprimer un commentaire</h1>
<p>Voulez-vous supprimer ce commentaire ?</p><blockquote><?php echo admin_escape($comment['libComment']); ?></blockquote>
<form action="<?php echo admin_api_url('comments', 'delete'); ?>" method="post"><input type="hidden" name="idUserC1" value="<?php echo $idUserC1; ?>" /><input type="hidden" name="idUserC2" value="<?php echo $idUserC2; ?>" /><a href="list.php" class="btn btn-secondary">Annuler</a> <button type="submit" class="btn btn-danger">Supprimer</button></form>
</main>
<?php include '../../../footer.php'; ?>

