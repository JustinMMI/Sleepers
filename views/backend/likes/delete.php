<?php
include '../../../header.php';
require_once dirname(__DIR__) . '/admin_helpers.php';
$idUserL1 = admin_query_id('idUserL1');
$idUserL2 = admin_query_id('idUserL2');
$likes = sql_select('LIKES', '*', 'idUserL1 = ' . $idUserL1 . ' AND idUserL2 = ' . $idUserL2);
if (!$likes) { admin_redirect('/views/backend/likes/list.php'); }
$like = $likes[0];
?>

<main class="container py-4"><h1 class="mb-4">Supprimer un like</h1><p>Voulez-vous supprimer cette relation (<?php echo $idUserL1; ?> vers <?php echo $idUserL2; ?>) ?</p><form action="<?php echo admin_api_url('likes', 'delete'); ?>" method="post"><input type="hidden" name="idUserL1" value="<?php echo $idUserL1; ?>" /><input type="hidden" name="idUserL2" value="<?php echo $idUserL2; ?>" /><a href="list.php" class="btn btn-secondary">Annuler</a> <button type="submit" class="btn btn-danger">Supprimer</button></form></main>
<?php include '../../../footer.php'; ?>

