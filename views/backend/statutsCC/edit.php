<?php
include '../../../header.php';

if (!isset($_GET['numStat']) || filter_var($_GET['numStat'], FILTER_VALIDATE_INT) === false) {
	header('Location: list.php');
	exit;
}

$numStat = (int) $_GET['numStat'];
$statuts = sql_select('STATUT', '*', 'numStat = ' . $numStat);

if (!$statuts) {
	header('Location: list.php');
	exit;
}

$statut = $statuts[0];
?>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1>Modification du statut</h1>
		</div>
		<div class="col-md-12">
			<form action="<?php echo ROOT_URL . '/api/statutsCC/update.php'; ?>" method="post">
				<input type="hidden" name="numStat" value="<?php echo (int) $statut['numStat']; ?>" />
				<div class="form-group">
					<label for="libStat">Nom du statut</label>
					<input id="libStat" name="libStat" class="form-control" type="text" value="<?php echo htmlspecialchars($statut['libStat'], ENT_QUOTES, 'UTF-8'); ?>" required autofocus="autofocus" />
				</div>
				<br />
				<div class="form-group mt-2">
					<a href="list.php" class="btn btn-primary">List</a>
					<button type="submit" class="btn btn-success">Confirmer la modification</button>
				</div>
			</form>
		</div>
	</div>
</div>
