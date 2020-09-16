<?php
	require_once('connect.php');
	// get table info for update
	$table = $_REQUEST['table'];
	$key = $_REQUEST['key'];
	$result = mysqli_query($db, "SELECT * FROM $table LIMIT 0;");
	$query = "UPDATE $table SET ";
	// build string for insert
	mysqli_field_seek($result, 1);
	while( $col = mysqli_fetch_field($result) )
	{
		$colname = $col->name;
		$coltype = $col->type;
		if(!isset($_REQUEST["$colname"]) && $coltype == 1)
		{$data = 0;}
		else
		{
			$data = $_REQUEST["$colname"];
			$data = str_replace("'", "\'", $data);
		}
		$query = $query."$colname = '$data', ";
	}
	// remove last space and comma from string
	$query = substr($query, 0, -2)." WHERE ID = $key;";
	// update database
	$result = mysqli_query($db, $query);
	// redirect user to appropriate destination, with error message if necessary
	if( empty( mysqli_error($db) ) )
	{
		// set variables for adjacent keys
		$result = mysqli_query($db, "SELECT MIN(ID) FROM $table WHERE ID > $key;") or die("Error! Can't load data!");
		$row = mysqli_fetch_row($result);
		$keynext = $row[0];
		$result = mysqli_query($db, "SELECT MAX(ID) FROM $table WHERE ID < $key;") or die("Error! Can't load data!");
		$row = mysqli_fetch_row($result);
		$keyprev = $row[0];
		$result = mysqli_query($db, "SELECT MAX(ID) FROM $table;") or die("Error! Can't load data!");
		$row = mysqli_fetch_row($result);
		$keymax = $row[0];
		$result = mysqli_query($db, "SELECT MIN(ID) FROM $table;") or die("Error! Can't load data!");
		$row = mysqli_fetch_row($result);
		$keymin = $row[0];
		if( empty($keynext) ) {$keynext = $keymin;}
		if( empty($keyprev) ) {$keyprev = $keymax;}
		// set destination		
		$action = $_REQUEST['action'];
		if($action == '< Save') {header( 'Location: edit.php?table='.urlencode($table).'&key='.urlencode($keyprev) );}
		else if($action == 'Save & Return') {header( 'Location: index.php?table='.urlencode($table) );}
		else if($action == 'Save >') {header( 'Location: edit.php?table='.urlencode($table).'&key='.urlencode($keynext) );}
	}
	else {header( 'Location: edit.php?table='.urlencode($table).'&key='.$key.'&update_fail' );}
?>