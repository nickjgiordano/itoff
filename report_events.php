<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
		<script type="text/javascript" src="script.js"></script>
		<link rel="stylesheet" type="text/css" href="report.css">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
		<title>Report | iT-off | Forthcoming events</title>
	</head>
	<body>
		<?php
			require_once('connect.php');
			
			// select data for page display
			$query = 'SELECT Event.ID, Start_Date, Course_Title, Course_Fee, Duration, Venue_Name, Hotel_Fee ' .
			'FROM (Event INNER JOIN Course ON Course_ID = Course.ID) INNER JOIN Venue ON Venue_ID = Venue.ID ' .
			'WHERE Start_Date > NOW() ORDER BY Start_Date LIMIT 10;';
			$result = mysqli_query($db, $query) or die("Error! Can't load data!");
			
			// begin page containing table
			echo '<div class="border_t"></div><div class="border_l"></div><div class="page">';
			
			// include header page with logo, and create title section
			include('report_header.php');
			echo '<h1>Forthcoming events</h1>' .
			'<p>The following is a list of the next ten iT-off course events, including course titles, venues, dates, and prices:</p>';
			
			// create header row of table
			echo '<table class="horizontal"><tr class="title">' .
			'<td>ID</td>' .
			'<td>Course</td>' .
			'<td>Venue</td>' .
			'<td>Start Date</td>' .
			'<td>End Date</td>' .
			'<td>Course Fee</td>' .
			'<td>Hotel Fee</td>' .
			'</tr>';
			
			// populate table with data, looping through rows
			while( $row = mysqli_fetch_assoc($result) )
			{
				// get data to populate table
				$id = $row['ID'];
				$course = $row['Course_Title'];
				$venue = $row['Venue_Name'];
				$coursefee = $row['Course_Fee'];
				$hotelfee = $row['Hotel_Fee'];
				
				// format start date
				$start = $row['Start_Date'];
				$startdate = date_create($start);
				$startformat = date_format($startdate, 'Y-m-d');
				
				// format duration
				$duration = $row['Duration'];
				$durationdays = date_interval_create_from_date_string($duration.' days');
				
				// calculate and format end date
				$enddate = date_add($startdate, $durationdays);
				$endformat = date_format($enddate, 'Y-m-d');
				
				// create data row
				echo '<tr>' .
				'<td>'.$id.'</td>' .
				'<td class="text">'.$course.'</td>' .
				'<td class="text">'.$venue.'</td>' .
				'<td>'.$startformat.'</td>' .
				'<td>'.$endformat.'</td>' .
				'<td class="number">£'.$coursefee.'</td>' .
				'<td class="number">£'.$hotelfee.'</td>' .
				'</tr>';
			}
			
			// create table footer and end page
			echo '<tr class="summary"><td colspan ="7"></td></tr></table>' .
			'<p class="note">See overleaf for the attached booking form.</p>' .
			'</div><div class="border_r"></div><div class="border_b"></div><footer></footer>';
			
			// begin page containing form
			echo '<div class="border_t"></div><div class="border_l"></div><div class="page">';
			
			// include header page for logo
			include('report_header.php');
			
			// create form for printing
			echo '<h1>Booking form</h1>' .
			'<h3>Name</h3><div class="field"></div>' .
			'<h3>Address</h3><div class="field"></div><div class="field"></div><div class="field"></div><div class="field"></div>' .
			'<h3>Telephone</h3><div class="field"></div>' .
			'<table style="width: 100%;">' .
			'<tr><td></td><td style="width: 200px;"><h3>Event ID</h3></td><td></td><td style="width: 200px;"><h3>Bed & Breakfast (tick)</h3></td><td></td></tr>' .
			'<tr><td></td><td><div class="field" style="width: 35px; margin: auto;"></div></td><td></td><td><div class="field" style="width: 35px; margin: auto;"></div></td><td></td></tr>' .
			'</table>' .
			'</div><div class="border_r"></div><div class="border_b"></div><footer></footer>';
			
			// free results and close database
			mysqli_free_result($result);
			mysqli_close($db);
		?>
	</body>
</html>