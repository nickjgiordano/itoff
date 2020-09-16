<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
		<script type="text/javascript" src="script.js"></script>
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
		<title>Edit data | iT-off</title>
	</head>
	<body onload="formFocus()">
		<?php
			require_once('nav.php');
			// if table uses view, set prefix
			$table = $_REQUEST['table'];
			if(array_search($table, $tables_fk) > -1) {$view = 'form'.$table;} else {$view = $table;}
			// create form, including hidden fields
			$key = $_REQUEST['key'];
			echo '<div class="content_wrapper"><div class="form"><form action="update.php" method="get" id="edit" name="edit">' .
			'<input type="hidden" name="table" value="'.$table.'" />' .
			'<input type="hidden" name="key" value="'.$key.'" />';
			$result = mysqli_query($db, "SELECT * FROM $view WHERE ID = $key;") or die("Error! Can't load data!");
			$row = mysqli_fetch_assoc($result);
			mysqli_field_seek($result, 1);
			while( $col = mysqli_fetch_field($result) )
			{
				// get data for form
				$colname = $col->name;
				$coltype = $col->type;
				$colsize = $col->length;
				$colalias = str_replace('_', ' ', $colname);
				$data = $row[$colname];
				// if column is foreign key, create select list
				if(substr($colname, -3) == '_fk')
				{
					$colname = substr($colname, 0, -3);
					$colalias = substr($colalias, 0, -6);
					echo '<label for="'.$colname.'">'.$colalias.'</label><select name="'.$colname.'" form="edit">';
					$result_list = mysqli_query($db, 'SELECT * FROM list'.$colalias.';') or die("Error! Can't load data!");
					mysqli_data_seek($result_list, 0);
					while( $row_list = mysqli_fetch_assoc($result_list) )
					{
						$option = $row_list['list'];
						$keystart = strrpos($option, "[") + 1;
						$keylength = strrpos($option, "]") - $keystart;
						$keyvalue = substr($option, $keystart, $keylength);
						echo '<option value="'.$keyvalue.'" ';
						if($option == $data) {echo 'selected';}
						echo '>'.$option.'</option>';
					}
					echo '</select>';
				}
				// otherwise if column has Boolean data type, create checkbox
				else if($coltype == 1)
				{
					echo '<p><label for="'.$colname.'">'.$colalias.'</label><br /><input type="checkbox" name="'.$colname.'" value="1" ';
					if($data == 1) {echo 'checked';}
					echo ' /></p>';
				}
				// otherwise if column has Date data type, create date field
				else if($coltype == 7 || ($coltype >= 10 && $coltype <= 13) )
				{
					echo '<label for="'.$colname.'">'.$colalias.'</label><input type="date" name="'.$colname .
					'" value="'.$data.'" />';
				}
				// otherwise if column has any other data type, create text field
				else
				{
					echo '<label for="'.$colname.'">'.$colalias.'</label><input type="text" name="'.$colname .
					'" value="'.$data.'" maxlength="'.$colsize.'" />';
				}
			}
			// display error message for update or delete fail
			if( isset($_REQUEST['update_fail']) )
			{echo '<div class="error"><p>Error! The record couldn\'t be updated!</p><p>Duplicate records aren\'t allowed!</p></div>';}
			if( isset($_REQUEST['delete_fail']) )
			{echo '<div class="error"><p>Error! The record couldn\'t be deleted!</p><p>A foreign key is used elsewhere!</p></div>';}
		
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
		?>
					<table>
						<tr class="btn_reset">
							<td colspan="3"><input type="reset" value="Reset form" onclick="formFocus()" /></td>
						</tr>
						<tr class="btn_save">
							<td><input type="submit" name="action" value="< Save" onclick="return validation()" /></td>
							<td><input type="submit" name="action" value="Save & Return" onclick="return validation()" /></td>
							<td><input type="submit" name="action" value="Save >" onclick="return validation()" /></td>
						</tr>
						<tr class="btn_delete">
							<td><input type="submit" name="action" value="< Delete" formaction="delete.php" /></td>
							<td><input type="submit" name="action" value="Delete & Return" formaction="delete.php" /></td>
							<td><input type="submit" name="action" value="Delete >" formaction="delete.php" /></td>
						</tr>
					</form>
						<tr class="btn_cancel">
							<td>
								<form action="edit.php" method="get" name="previous">
									<input type="hidden" name="table" value="<?php echo $table ?>" />
									<input type="hidden" name="key" value="<?php echo $keyprev ?>" />
									<input type="submit" value="< Skip" />
								</form>
							</td>
							<td>
								<form action="index.php" method="get" name="cancel">
									<input type="hidden" name="table" value="<?php echo $table ?>" />
									<input type="submit" value="Cancel" />
								</form>
							</td>
							<td>
								<form action="edit.php" method="get" name="next">
									<input type="hidden" name="table" value="<?php echo $table ?>" />
									<input type="hidden" name="key" value="<?php echo $keynext ?>" />
									<input type="submit" value=" Skip >" />
								</form>
							</td>
						</tr>
					</table>
				</div>
			</div>
		<?php
			// free results and close database
			if( isset($result_list) ) {mysqli_free_result($result_list);}
			mysqli_free_result($result);
			mysqli_close($db);
		?>
	</body>
</html>