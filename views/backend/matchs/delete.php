<?php
include '../../../header.php';
require_once dirname(__DIR__) . '/admin_helpers.php';
$idUserM1 = admin_query_id('idUserM1');
$idUserM2 = admin_query_id('idUserM2');
$matchs = sql_select('MATCHS', '*', 'idUserM1 = ' . $idUserM1 . ' AND idUserM2 = ' . $idUserM2);
if (!$matchs) { admin_redirect('/views/backend/matchs/list.php'); }
?>

<main class="container py-4"><h1 class="mb-4">Supprimer un match</h1><p>Voulez-vous supprimer le match entre les utilisateurs <?php echo $idUserM1; ?> et <?php echo $idUserM2; ?> ?</p><form action="<?php echo admin_api_url('matchs', 'delete'); ?>" method="post"><input type="hidden" name="idUserM1" value="<?php echo $idUserM1; ?>" /><input type="hidden" name="idUserM2" value="<?php echo $idUserM2; ?>" /><a href="list.php" class="btn btn-secondary">Annuler</a> <button type="submit" class="btn btn-danger">Supprimer</button></form></main>
<?php include '../../../footer.php'; ?>

