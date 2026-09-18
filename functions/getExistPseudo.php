<?php
// The current USER model has no pseudo field; email is the login identifier.
function get_ExistPseudo($emailUser){
	global $DB;

    //connect to database
    if(!$DB){
        sql_connect();
    }

	$query = 'SELECT idUser FROM `USER` WHERE emailUser = ? LIMIT 1;';
	$result = $DB->prepare($query);
	$result->execute(array($emailUser));
	return $result->fetchColumn() !== false;
}
?>