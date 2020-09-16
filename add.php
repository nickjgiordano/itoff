<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
		<script type="text/javascript" src="script.js"></script>
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
		<title>Add data | iT-off</title>
	</head>
	<body onload="formFocus()">
		<?php
			require_once('nav.php');
			// if table uses view, set prefix
			$table = $_REQUEST['table'];
			if(array_search($table, $tables_fk) > -1) {$view = 'form'.$table;} else {$view = $table;}
			// create form, including hidden fields
			echo '<div class="content_wrapper"><div class="form"><form action="insert.php" method="get" id="add" name="add">' .
			'<input type="hidden" name="table" value="'.$table.'" />';
			$result = mysqli_query($db, "SELECT * FROM $view;") or die("Error! Can't load data!");
			mysqli_field_seek($result, 1);
			while( $col = mysqli_fetch_field($result) )
			{
				// get data for form
				$colname = $col->name;
				$coltype = $col->type;
				$colsize = $col->length;
				$colalias = str_replace('_', ' ', $colname);
				// if column is foreign key, create select list
				if(substr($colname, -3) == '_fk')
				{
					$colname = substr($colname, 0, -3);
					$colalias = substr($colalias, 0, -6);
					echo '<label for="'.$colname.'">'.$colalias.'</label><select name="'.$colname.'" form="add"><option></option>';
					$result_list = mysqli_query($db, 'SELECT * FROM list'.$colalias.';') or die("Error! Can't load data!");
					mysqli_data_seek($result_list, 0);
					while( $row_list = mysqli_fetch_assoc($result_list) )
					{
						$option = $row_list['list'];
						$keystart = strrpos($option, "[") + 1;
						$keylength = strrpos($option, "]") - $keystart;
						$keyvalue = substr($option, $keystart, $keylength);
						echo '<option value="'.$keyvalue.'">'.$option.'</option>';
					}
					echo '</select>';
				}
				// otherwise if column has Boolean data type, create checkbox
				else if($coltype == 1)
				{
					echo '<p><label for="'.$colname.'">'.$colalias.'</label><br /><input type="checkbox" name="'.$colname.'" value="1" /></p>';
				}
				// otherwise if column has Date data type, create date field
				else if($coltype == 7 || ($coltype >= 10 && $coltype <= 13) )
				{
					echo '<label for="'.$colname.'">'.$colalias.'</label><input type="date" name="'.$colname.'" value="" />';
				}
				// otherwise if column has any other data type, create text field
				else
				{
					echo '<label for="'.$colname.'">'.$colalias.'</label><input type="text" name="'.$colname.'" value="" maxlength="'.$colsize.'" />';
				}
			}
			// display error message for insert fail
			if( isset($_REQUEST['insert_fail']) )
			{echo '<div class="error"><p>Error! Your record couldn\'t be added!</p><p>Duplicate records aren\'t allowed!</p></div>';}
		?>
					<table>
						<tr class="btn_reset"><td><input type="reset" value="Reset form" onclick="formFocus()" /></td></tr>
						<tr class="btn_save"><td><input type="submit" name="action" value="Save & Add Another" onclick="return validation()" /></td></tr>
						<tr class="btn_save"><td><input type="submit" name="action" value="Save & Return to Table" onclick="return validation()" /></td></tr>
					</form>
					<form action="index.php" method="get" name="cancel">
						<input type="hidden" name="table" value="<?php echo $table ?>" />
						<tr class="btn_cancel"><td><input type="submit" value="Cancel" /></td></tr>
					</form>
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