<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
		<script type="text/javascript" src="script.js"></script>
		<link rel="stylesheet" type="text/css" href="report.css">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
		<title>Report | iT-off | Booking confirmation slip</title>
	</head>
	<body>
		<?php
			require_once('connect.php');
			
			// select data for page display
			$key = $_REQUEST['key'];
			$query = "SELECT Event_ID, Delegate_ID, Presenter, Bed_and_Breakfast, " .
			"Start_Date, Course_Title, Course_Fee, Duration, Venue_Name, Hotel_Fee, Surname, Forename, Delegate.Address, Delegate.Telephone " .
			"FROM (((Booking INNER JOIN Event ON Event_ID = Event.ID) " .
			"INNER JOIN Course ON Course_ID = Course.ID) " .
			"INNER JOIN Venue ON Venue_ID = Venue.ID) " .
			"INNER JOIN Delegate ON Delegate_ID = Delegate.ID " .
			"WHERE Booking.ID = $key";
			$result = mysqli_query($db, $query) or die("Error! Can't load data!");
			
			// get data
			$row = mysqli_fetch_assoc($result);
			$delegate = $row['Delegate_ID'];
			$surname = $row['Surname'];
			$forename = $row['Forename'];
			$address = $row['Address'];
			$telephone = $row['Telephone'];
			
			// format address onto multiple lines
			$addressformat = str_replace(', ', '<br />', $address);
			$addressformat = str_replace(',', '<br />', $address);
			$addressformat = $forename.' '.$surname.'<br />'.$addressformat.'<br />'.$telephone;
			
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
			$event = $row['Event_ID'];
			$course = $row['Course_Title'];
			$venue = $row['Venue_Name'];
			$presenter = $row['Presenter'];
			$hotel = $row['Bed_and_Breakfast'];
			$coursefee = $row['Course_Fee'];
			$hotelfee = $row['Hotel_Fee'] * ($duration-1);
			
			// set course and hotel fees to 0 if necessary
			if($presenter)
			{
				$coursefee = 0;
				$hotelfee = 0;
			}
			else if(!$hotel) {$hotelfee = 0;}
			
			// begin page
			echo '<div class="border_t"></div><div class="border_l"></div><div class="page">';
			
			// include header page for logo
			include('report_header.php');
			
			// create address and title section
			echo '<div class="address"><div class="left">'.$addressformat.'</div><div class="right">' .
			'iT-off Limited<br />Oakwood Park<br />Tonbridge Road<br />Maidstone<br />ME16 8AQ<br />07395734367</div></div>' .
			'<h1>Booking confirmed!</h1>' .
			'<p>Thank you <span style="font-weight: bold;">'.$forename.' '.$surname .
			'</span>! Your personal ID is <span style="font-weight: bold;">'.$delegate .
			'</span>, and your booking number is <span style="font-weight: bold;">'.$key.'</span>.</p>' .
			'<p>We\'ve confirmed your booking, and you can view the details below:</p>';
			
			// display data
			echo '<table class="vertical">' .
			'<tr><td class="title">Event ID: </td><td>'.$event.'</td></tr>' .
			'<tr><td class="title">Course: </td><td>'.$course.'</td></tr>' .
			'<tr><td class="title">Venue: </td><td>'.$venue.'</td></tr>' .
			'<tr><td class="title">Start Date: </td><td>'.$startformat.'</td></tr>' .
			'<tr><td class="title">End Date: </td><td>'.$endformat.'</td></tr>' .
			'<tr><td class="title">Course Fee: </td><td>£'.number_format($coursefee, 2).'</td></tr>' .
			'<tr><td class="title">Bed & Breakfast: </td>';
			if($hotel) {echo '<td>yes</td></tr>';} else if(!$hotel) {echo '<td>no</td></tr>';}
			echo '<tr><td class="title">Hotel Fee: </td><td>£'.number_format($hotelfee,2).'</td></tr>' .
			'<tr><td class="title">Total Fee: </td><td>£'.number_format($coursefee+$hotelfee, 2).'</td></tr>' .
			'</table>';
			
			// note at end of data and end page
			echo '<div class="note">NOTE: You need to collect your ID badge from venue reception on the first day of the course.</div>' .
			'</div><div class="border_r"></div><div class="border_b"></div>';
			
			// free results and close database
			mysqli_free_result($result);
			mysqli_close($db);
		?>
	</body>
</html>