<?php
	require_once('connect.php');
	// get table info for insert
	$table = $_REQUEST['table'];
	$result = mysqli_query($db, "SELECT * FROM $table LIMIT 0;");
	mysqli_field_seek($result, 1);
	$query = "INSERT INTO $table (";
	// get column names to build string for insert
	while( $col = mysqli_fetch_field($result) )
	{
		$colname = $col->name;
		$query = $query."$colname, ";
	}
	// remove last space and comma from string
	$query = substr($query, 0, -2).") VALUES (";
	// get data to build string for insert
	mysqli_field_seek($result, 1);
	while( $col = mysqli_fetch_field($result) )
	{
		$colname = $col->name;
		$coltype = $col->type;
		if(!isset($_REQUEST["$colname"]) && $coltype == 1) {$data = 0;}
		else
		{
			$data = $_REQUEST["$colname"];
			$data = str_replace("'", "\'", $data);
		}
		$query = $query."'$data', ";
	}
	// remove last space and comma from string
	$query = substr($query, 0, -2).");";
	// insert data into database
	$result = mysqli_query($db, $query);
	// redirect user to appropriate destination, with error message if necessary
	if( empty( mysqli_error($db) ) )
	{
		$action = $_REQUEST['action'];
		if($action == 'Save & Add Another') {header( 'Location: add.php?table='.urlencode($table) );}
		else if ($action = 'Save & Return to Table') {header( 'Location: index.php?table='.urlencode($table) );}
	}
	else {header( 'Location: add.php?table='.urlencode($table).'&key='.$key.'&insert_fail' );}
?>