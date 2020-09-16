<?php
	require_once('connect.php');
	// delete data from database
	$table = $_REQUEST['table'];
	$key = $_REQUEST['key'];
	$result = mysqli_query($db, "DELETE FROM $table WHERE ID = $key;");
	// create link to redirect user, maintaining filters and sorts
	$header = 'Location: index.php?table='.urlencode($table);
	if( isset($_REQUEST['filter']) )
	{
		$filter = $_REQUEST['filter'];
		$criterion = $_REQUEST['criterion'];
		$header = $header.'&filter='.urlencode($filter).'&criterion='.urlencode($criterion);
	}
	if( isset($_REQUEST['sort']) )
	{
		$sort = $_REQUEST['sort'];
		$order = $_REQUEST['order'];
		$header = $header.'&sort='.urlencode($sort).'&order='.urlencode($order);
	}
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
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == '< Delete') {header( 'Location: edit.php?table='.urlencode($table).'&key='.urlencode($keyprev) );}
		else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'Delete >') {header( 'Location: edit.php?table='.urlencode($table).'&key='.urlencode($keynext) );}
		else {header($header);}
	}
	else
	{
		if( !isset($_REQUEST['action']) ) {header($header.'&delete_fail='.$key);}
		else {header( 'Location: edit.php?table='.urlencode($table).'&key='.urlencode($key).'&delete_fail' );}
	}
?>