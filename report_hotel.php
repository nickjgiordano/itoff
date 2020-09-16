<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
		<script type="text/javascript" src="script.js"></script>
		<link rel="stylesheet" type="text/css" href="report.css">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
		<title>Report | iT-off | Bed and breakfast request</title>
	</head>
	<body>
		<?php
			require_once('connect.php');
			
			// select data for page display
			$key = $_REQUEST['key'];
			$query = "SELECT Start_Date, Duration, Venue_Name, Surname, Forename, Venue.Address, Venue.Telephone " .
			"FROM (((Booking INNER JOIN Event ON Event_ID = Event.ID) " .
			"INNER JOIN Course ON Course_ID = Course.ID) " .
			"INNER JOIN Venue ON Venue_ID = Venue.ID) " .
			"INNER JOIN Delegate ON Delegate_ID = Delegate.ID " .
			"WHERE Booking.ID = $key";
			$result = mysqli_query($db, $query) or die("Error! Can't load data!");
			
			// get  data
			$row = mysqli_fetch_assoc($result);
			$name = $row['Venue_Name'];
			$address = $row['Address'];
			$telephone = $row['Telephone'];
			
			// format address onto multiple lines
			$addressformat = str_replace(', ', '<br />', $address);
			$addressformat = str_replace(',', '<br />', $address);
			$addressformat = $name.'<br />'.$addressformat.'<br />'.$telephone;
			
			// format start date
			$start = $row['Start_Date'];
			$startdate = date_create($start);
			$startformat = date_format($startdate, 'l jS F Y');
			
			// format duration
			$duration = $row['Duration'];
			$durationdays = date_interval_create_from_date_string( ($duration-1).' days' );
			
			// calculate and format end date
			$enddate = date_add($startdate, $durationdays);
			$endformat = date_format($enddate, 'l jS F Y');
			
			// get more data
			$surname = $row['Surname'];
			$forename = $row['Forename'];
			
			// begin page
			echo '<div class="border_t"></div><div class="border_l"></div><div class="page">';
			
			// include header page for logo
			include('report_header.php');
			
			// create address and title section
			echo '<div class="address"><div class="left">'.$addressformat.'</div><div class="right">' .
			'iT-off Limited<br />Oakwood Park<br />Tonbridge Road<br />Maidstone<br />ME16 8AQ<br />07395734367</div></div>' .
			'<h1>New booking!</h1>' .
			'<p>'.$name .',</p><p>We\'ve received a new hotel booking request, as specified below:</p>';
			
			// display data
			echo '<table class="vertical">' .
			'<tr><td class="title">Name: </td><td>'.$forename.' '.$surname.'</td></tr>' .
			'<tr><td class="title">Check-in: </td><td>'.$startformat.'</td></tr>' .
			'<tr><td class="title">Check-out: </td><td>'.$endformat.'</td></tr>' .
			'</table>';
			
			// note at end of data and end page
			echo '<div class="note">NOTE: Guest has been informed of booking, and will pay upon checking in on the first day of the course.</div>' .
			'</div><div class="border_r"></div><div class="border_b"></div>';
			
			// free results and close database
			mysqli_free_result($result);
			mysqli_close($db);
		?>
	</body>
</html>