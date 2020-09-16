<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
		<script type="text/javascript" src="script.js"></script>
		<link rel="stylesheet" type="text/css" href="report.css">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
		<title>Report | iT-off | Badge labels</title>
	</head>
	<body>
		<?php
			require_once('connect.php');
			
			// get venue data
			$key = $_REQUEST['key'];
			$query = "SELECT Venue_Name FROM Event INNER JOIN Venue ON Venue_ID = Venue.ID WHERE Event.ID = $key;";
			$result = mysqli_query($db, $query) or die("Error! Can't load data!");
			$row = mysqli_fetch_assoc($result);
			$venue = $row['Venue_Name'];
			
			// get data, to get number of rows, to calculate number of pages required
			$query = "SELECT Event_ID FROM Booking WHERE Event_ID = $key;";
			$result = mysqli_query($db, $query) or die("Error! Can't load data!");
			$nrows = mysqli_num_rows($result);
			$pages = ceil($nrows / 10);
			
			// if there are no rows, display message; otherwise loop through each page, creating badge labels
			if($nrows == 0) {echo 'No delegate badges to print!';}
			else
			{
				for($i = 0; $i < $pages; $i++)
				{			
					// begin page
					echo '<div class="border_t"></div><div class="border_l"></div><div class="page">';
					
					// select data for badges, determined by page number, selecting only 10 records
					$query = "SELECT Surname, Forename, Presenter " .
					"FROM Booking INNER JOIN Delegate ON Delegate_ID = Delegate.ID " .
					"WHERE Event_ID = $key ORDER BY Surname LIMIT 10 OFFSET ".($i*10).";";
					$result = mysqli_query($db, $query) or die("Error! Can't load data!");
					
					echo '<table class="badge">';
					// loop through data, creating 2 columns, each containing a badge if it exists
					mysqli_data_seek($result, 0);
					while( $row = mysqli_fetch_assoc($result) )
					{
						$presenter = $row['Presenter'];
						$surname = $row['Surname'];
						$forename = $row['Forename'];
						if($presenter) {echo '<tr><td><h5>PRESENTER</h5>'.$forename.' '.$surname.'<br /><img src="images/logo3.png">';}
						else if(!$presenter) {echo '<tr><td>'.$forename.' '.$surname.'<br /><img src="images/logo2.png">';}
						echo '<h5>iT-off #'.$key.' at '.$venue.'</h5></td>';
						if( $row = mysqli_fetch_assoc($result) )
						{
							$presenter = $row['Presenter'];
							$surname = $row['Surname'];
							$forename = $row['Forename'];
							if($presenter) {echo '<td><h5>PRESENTER</h5>'.$forename.' '.$surname.'<br /><img src="images/logo3.png">';}
							else if(!$presenter) {echo '<td>'.$forename.' '.$surname.'<br /><img src="images/logo2.png">';}
							echo '<h5>iT-off #'.$key.' at '.$venue.'</h5></td>';
						}
						echo '</tr>';
					}
					echo '</table>';
					
					// end page
					echo '</div><div class="border_r"></div><div class="border_b"></div><footer></footer>';
				}
			}
			
			// free results and close database
			mysqli_free_result($result);
			mysqli_close($db);
		?>
	</body>
</html>