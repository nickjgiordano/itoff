<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
		<script type="text/javascript" src="script.js"></script>
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
		<title>Database | iT-off</title>
	</head>
	<body onload="scrollPosition()">
		<?php
			require_once('nav.php');
			// if table is set, create table, otherwise display homepage with welcome message
			if( isset($_REQUEST['table']) )
			{
				// if table uses view, set prefix
				$table = $_REQUEST['table'];
				if(array_search($table, $tables_fk) > -1) {$view = 'view'.$table;} else {$view = $table;}
				
				// select data for page display, using filter and sort preferences
				$query = "SELECT * FROM $view";
				if( isset($_REQUEST['filter']) )
				{
					$filter = $_REQUEST['filter'];
					$filter_alias = str_replace('_', ' ', $filter);
					$criterion = $_REQUEST['criterion'];
					$query = "$query WHERE $filter = '$criterion'";
				}
				if( isset($_REQUEST['sort']) )
				{
					$sort = $_REQUEST['sort'];
					$order = $_REQUEST['order'];
					$query = "$query ORDER BY $sort $order";
				}
				else {$query = "$query ORDER BY ID";}
				$result = mysqli_query($db, $query.';') or die("Error! Can't load data!");
				
				// create select list row for filtering
				$ncolumns = mysqli_num_fields($result);
				echo '<br /><table class="data"><tr><td colspan="'.($ncolumns+4).'" class="radius_top"></td></tr><tr class="filter">';
				while( $col = mysqli_fetch_field($result) )
				{
					$colname = $col->name;
					$coltype = $col->type;
					$result_list = mysqli_query($db, "SELECT $colname FROM $view GROUP BY $colname ORDER BY $colname ASC;")
					or die("Error! Can't load data!");
					echo '<td><select id="filter" onchange="filter(\''.$table.'\', \''.$colname.'\', this)">';
					$colalias = str_replace('_', ' ', $colname);
					echo '<option value="'.$colname.'">'.$colalias.'</option>';
					mysqli_data_seek($result_list, 0);
					while( $row = mysqli_fetch_assoc($result_list) )
					{
						$data = $row[$colname];
						$value = $data;
						if($coltype == 1)
						{
							if($value) {$data = 'yes';} else if(!$value) {$data = 'no';}
						}
						echo '<option value="'.$value.'">'.$data.'</option>';
					}
					echo '</select></td>';
				}
				
				// create sort link row for sorting
				echo '<td colspan="4"></td></tr><tr class="sort">';
				mysqli_field_seek($result, 0);
				while( $col = mysqli_fetch_field($result) )
				{
					$colname = $col->name;
					echo '<td><a onclick="storePosition()" href="index.php?table='.urlencode($table);
					if (isset($_REQUEST['filter']) && $filter == $colname)
					{echo '"><div class="clear_x">x';}
					else
					{
						if( isset($filter) ) {echo '&filter='.urlencode($filter).'&criterion='.urlencode($criterion);}
						echo '&sort='.urlencode($colname).'&order=';
						
						if(isset($sort) && $sort == $colname && $order == 'ASC')
						{echo 'DESC"><div>&#x25B2;';}
						else if(isset($sort) && $sort == $colname && $order == 'DESC')
						{echo 'ASC"><div>&#x25BC;';}
						else
						{echo 'ASC"><div>&#x25AC;';}
					}
					echo '</div></a></td>';
				}
				echo '<td colspan="4"></td></tr>';
				
				// populate table with data, looping through rows
				while( $row = mysqli_fetch_assoc($result) )
				{
					// get data to populate table
					$key = $row['ID'];
					echo '<tr class="row">';
					mysqli_field_seek($result, 0);
					// create main columns of data
					while ( $col = mysqli_fetch_field($result) )
					{
						$colname = $col->name;
						$coltype = $col->type;
						$data = $row[$colname];
						if($coltype == 1)
						{
							if($data) {echo '<td class="col_yes">&#x2714;</td>';}
							else if(!$data) {echo '<td class="col_no">&#x2718;</td>';}
						}
						else if($coltype < 10)
						{
							echo '<td class="col_right">'.$data.'</td>';
						}
						else if($coltype == 246)
						{
							echo '<td class="col_right">£'.$data.'</td>';
						}
						else
						{
							echo '<td class="column">'.$data.'</td>';
						}
					}
					// create custom column for report buttons
					if($table == 'Booking')
					{
						echo '<td class="report"><a onclick="storePosition()" href="javascript: popup(\'report_confirm.php?key='.urlencode($key) .
						'\')"><div>booking<br />slip</div></a></td>';
						if($data)
						{
							echo '<td class="report"><a onclick="storePosition()" href="javascript: popup(\'report_hotel.php?key='.urlencode($key) .
							'\')"><div>B&B<br />request</div></a></td>';
						}
						else if(!$data) {echo '<td></td>';}
					}
					else if($table == 'Event')
					{
						echo '<td class="report"><a onclick="storePosition()" href="javascript: popup(\'report_register.php?key='.urlencode($key) .
						'\')"><div>event<br />register</div></a></td>' .
						'<td class="report"><a onclick="storePosition()" href="javascript: popup(\'report_badges.php?key='.urlencode($key) .
						'\')"><div>badge<br />sheet</div></a></td>';
					}
					else {echo '<td colspan="2"></td>';}
					// create edit link
					echo '<td class="edit"><a onclick="storePosition()" href="edit.php?table='.urlencode($table).'&key='.urlencode($key) .
					'"><div>EDIT</div></a></td><td class="delete';
					// create delete link
					if( isset($_REQUEST['delete_fail']) && $_REQUEST['delete_fail'] == $key ) {echo '_fail';}
					echo '"><a onclick="storePosition()" href="delete.php?table='.urlencode($table).'&key='.urlencode($key);
					if( isset($filter) )
					{echo '&filter='.urlencode($filter).'&criterion='.urlencode($criterion);}
					if( isset($sort) )
					{echo '&sort='.urlencode($sort).'&order='.urlencode($order);}
					echo '"><div>';
					if( isset($_REQUEST['delete_fail']) && $_REQUEST['delete_fail'] == $key ) {echo 'CAN\'T<br />DELETE';} else {echo 'DELETE';}
					echo '</div></a></td></tr>';
				}
				
				// create table footer, including add link
				$nrows = mysqli_num_rows($result);
				echo '<tr><td colspan="'.($ncolumns+4).'" class="empty_row">&nbsp;</td></tr>' .
				'<tr class="table_footer"><td colspan ="'.($ncolumns+2).'" class="table_info">Displaying '.$nrows.' record(s)';
				if( isset($filter) )
				{
					echo ' &nbsp; | &nbsp; <span>filtered by '.$filter_alias.' = '.$criterion.'</span>' .
					' &nbsp; | &nbsp; <a onclick="storePosition()" href="index.php?table='.urlencode($table).'" class="clear">clear filter</a>';
				}
				echo '</td><td colspan="2" class="add"><a href="add.php?table=' .
				urlencode($table).'"><div>ADD NEW</div></a></td></tr><tr><td colspan="'.($ncolumns+4).'" class="radius_bottom">&nbsp;</td></tr></table>';
				
				// free results and close database
				if( isset($result_list) ) {mysqli_free_result($result_list);}
				mysqli_free_result($result);
				mysqli_close($db);
			}
			else
			{
				echo '<div class="content_wrapper"><div class="content">' .
				'<h1 style="text-align: center;">iT-off database version 2.0</h1>' .
				'<p>Welcome to the iT-off Event & Booking Database! Click the links above to navigate the different tables and edit the database!</p>' .
				'<h2>Table descriptions</h2>' .
				'<p>The <span style="font-weight: bold;">Course</span> table contains all courses on offer from the company.</p>' .
				'<p>The <span style="font-weight: bold;">Venue</span> table contains all hotel venues used by the company for events.</p>' .
				'<p>The <span style="font-weight: bold;">Event</span> table contains all course events run by the company, past and future. ' .
				'From there, you can access the printable event register and delegate badge sheet.</p>' .
				'<p>The <span style="font-weight: bold;">Delegate</span> table contains all delegates and presenters to have booked events at any point.</p>' .
				'<p>The <span style="font-weight: bold;">Booking</span> table contains all event bookings made by delegates and presenters at any point. ' .
				'From there, you can access the printable booking confirmation slip and B&B request letter.</p>' .
				'<p>Click the below button to access a printable list of forthcoming events, along with an attached booking form:</p>' .
				'<p><table><tr><td class="report"><a onclick="storePosition()" href="javascript: popup(\'report_events.php' .
				'\')"><div>upcoming<br />events</div></a></td><td class="report_sep"></td></tr></table></p>' .
				'</div></div>';
			}
		?>
	</body>
</html>