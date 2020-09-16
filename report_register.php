<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
		<script type="text/javascript" src="script.js"></script>
		<link rel="stylesheet" type="text/css" href="report.css">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
		<title>Report | iT-off | Event register</title>
	</head>
	<body>
		<?php
			require_once('connect.php');
			
			// select data for page display
			$key = $_REQUEST['key'];
			$query = "SELECT Start_Date, Duration, Course_Title, Venue_Name " .
			"FROM (Event INNER JOIN Course ON Course_ID = Course.ID) " .
			"INNER JOIN Venue ON Venue_ID = Venue.ID " .
			"WHERE Event.ID = $key;";
			$result = mysqli_query($db, $query) or die("Error! Can't load data!");
			
			// get  data
			$row = mysqli_fetch_assoc($result);
			$course = $row['Course_Title'];
			$venue = $row['Venue_Name'];
			$start = $row['Start_Date'];
			$duration = $row['Duration'];
			
			// create start date and format duration
			$date = date_create($start);
			$interval = date_interval_create_from_date_string('1 day');
			
			// loop for page creation, changing date on each page
			for($i = 0; $i < $duration; $i++)
			{
				// format start date
				$dateformat = date_format($date, 'l jS F Y');
				
				// begin page
				echo '<div class="border_t"></div><div class="border_l"></div><div class="page">';
				
				// include header page for logo
				include('report_header.php');
				
				// create title section with date
				echo '<h1>Event '.$key.' -- Register</h1>' .
				'<h2>'.$course.', at '.$venue.'</h2>' .
				'<h4>'.$dateformat.'</h4>';
				
				// query database to get only presenters
				$query = "SELECT Surname, Forename, Presenter " .
				"FROM Booking INNER JOIN Delegate ON Delegate_ID = Delegate.ID " .
				"WHERE Event_ID = $key AND presenter = 1 ORDER BY Surname;";
				$result = mysqli_query($db, $query) or die("Error! Can't load data!");
				
				// display message depending on whether there are any presenters assigned to event
				$nrows = mysqli_num_rows($result);
				if($nrows == 0) {echo '<h5 style="color: red;">NOTE: Presenter(s) TBA</h5>';}
				else {echo '<h5>This event register is intended for use ONLY by the presenters listed below:</h5>';}
				
				// loop through data, displaying presenters
				mysqli_data_seek($result, 0);
				while( $row = mysqli_fetch_assoc($result) )
				{
					$presenter = $row['Presenter'];
					$surname = $row['Surname'];
					$forename = $row['Forename'];
					echo $forename.' '.$surname.'<br />';
				}
				
				echo '<h5>Delegate list:</h5>';
				// loop through data, displaying delegates, in 3 columns
				for($j = 0; $j < 3; $j++)
				{
					echo '<table class="register">';
					// query database to get only non-presenters
					$query = "SELECT Surname, Forename, Presenter " .
					"FROM Booking INNER JOIN Delegate ON Delegate_ID = Delegate.ID " .
					"WHERE Event_ID = $key AND presenter = 0 ORDER BY Surname LIMIT 16 OFFSET ".($j*16).";";
					$result = mysqli_query($db, $query) or die("Error! Can't load data!");
					
					// loop through data, displaying non-presenters
					mysqli_data_seek($result, 0);
					while( $row = mysqli_fetch_assoc($result) )
					{
						$presenter = $row['Presenter'];
						$surname = $row['Surname'];
						$forename = $row['Forename'];
						echo '<tr><td class="box"></td><td class="delegate">'.$surname.', '.$forename.'</td></tr>';
					}
					echo '</table>';
				}
				
				// add 1 day to date and end page
				$date = date_add($date, $interval);
				echo '</div><div class="border_r"></div><div class="border_b"></div><footer></footer>';
			}
			
			// free results and close database
			mysqli_free_result($result);
			mysqli_close($db);
		?>
	</body>
</html>