<?php


/*
    Connect to the database
*/
/**
 * Connect to database
 *
 * @param  void
 * @return PDO Object Connection link
 */
function database_connect(){
	$connection = false;
	global $databaseConfig;
	try {
		$dns = 'mysql:host=' . $databaseConfig["database_adress"] . ';dbname='.$databaseConfig["database_name"];
		$utilisateur = $databaseConfig["database_username"];
		$motDePasse = $databaseConfig["database_password"];
		$connection = new PDO($dns, $utilisateur, $motDePasse, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	} catch ( Exception $e ) {
		echo "Error function database_connect : ", $e->getMessage();
	}

	return $connection;
}

/**
 * Select data from database
 *
 * @param  PDO Object Connection link
 * @param  string     SQL raw query
 * @return array	  List of results
 */
function database_select($connection,$query){
	$result = array();

	$statement = $connection->prepare($query);
	if($statement->execute()) {
		$result = $statement->fetchAll();
	} else {
		$error_message = $statement->errorInfo();
		$result['status'] = "Error function database_select : $query".$error_message[2];
		die($result['status']);
	}

	return $result;
}


/**
 * Delete data from database
 * Issue with some conditions >, >=, <, <= that returns 1 whatever the number of data deleted to be fixed
 *
 * @param  PDO Object Connection link
 * @param  string     SQL raw query
 * @return void
 */
function database_delete($connection,$query){
		$result = '';

	$statement = $connection->prepare($query);

	if($statement->execute()) {
		if($statement->rowCount()>0)
			$result['status'] = $statement->rowCount();
		else
			$result['status'] = 'No row updated.';
	} else {
		$error_message = $statement->errorInfo();
		$result['status'] = 'Error function database_delete : '.$error_message[2];
		die($result['status']);
	}

	return $result;
}

function database_insert_pdo($connection, $table, $colonnes){
	$query = "INSERT INTO " . $table . "(";
	foreach ($colonnes as $colonne => $value)
		$query .= $colonne . ",";
	$query = rtrim($query, ',');
	$query .= ") VALUES(";
	foreach ($colonnes as $colonne => $value)
		$query .= ($colonne == 'date_creation' || $colonne == 'date_modification' || $colonne == 'date_maj' ? "NOW()" : ":" . $colonne) . ",";
	$query = rtrim($query, ',');
	$query .= ")";
	error_log("query insert PDO = " . $query);
	$stmt = $connection->prepare($query);
	foreach ($colonnes as $colonne => $value) {
		if (!in_array($colonne, array("date_creation", "date_modification", "date_maj")))
		$colonnes[':' . $colonne] = $value;
		unset($colonnes[$colonne]);
	}
	error_log("colonnes = " . json_encode($colonnes));
	$call =	$stmt->execute($colonnes);
	//$stmt->execute(array(':field1' => $field1, ':field2' => $field2, ':field3' => $field3, ':field4' => $field4, ':field5' => $field5));
	if($call) {
		$result['status'] = 1;
		$result['id'] = $connection->lastInsertId();
	} 
	else {
		$error_message = $stmt->errorInfo();
		$result['status'] = -1;
		$result['error'] = 'Error function database_insert : '.$error_message[2];
	}
	error_log("result2 = " . json_encode($result));
	return $result;
}


function database_update_pdo($connection, $table, $colonnes, $where){
	$query = "UPDATE " . $table . 
	$query .= " SET ";
	foreach ($colonnes as $colonne => $value)
		$query .= "$colonne = :$colonne" . ",";
	$query = rtrim($query, ',');
	//$query .= "$colonne = $value";
	$query .= " WHERE 1";
	foreach ($where as $colonne => $value)
		$query .= " AND $colonne = :$colonne";
	error_log("query update PDO = " . $query);
	$stmt = $connection->prepare($query);
	$params = array();
	foreach ($colonnes as $colonne => $value) {
		if (!in_array($colonne, array("date_creation", "date_modification", "date_maj")))
			$params[':' . $colonne] = $value;
	}
	foreach ($where as $colonne => $value) {
		if (!in_array($colonne, array("date_creation", "date_modification", "date_maj")))
			$params[':' . $colonne] = $value;
	}
	error_log("params = " . json_encode($params));
	$call =$stmt->execute($params);
	//$stmt->execute(array(':field1' => $field1, ':field2' => $field2, ':field3' => $field3, ':field4' => $field4, ':field5' => $field5));
	if($call) {
		$result['status'] = 1;
		$result['message'] = $stmt->rowCount();
	} 
	else {
		$error_message = $stmt->errorInfo();
		$result['status'] = -1;
		$result['error'] = 'Error function database_update_pdo : '.$error_message[2];
	}
	error_log("result = " . json_encode($result));
	return $result;
}