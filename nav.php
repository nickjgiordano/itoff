<!DOCTYPE html>
<html>
	<head></head>
	<body>
		<?php
			require_once('connect.php');
		?>
		<div class="top">
			<div class="top_info">
				<div class="left">
					iT-off database v2.0
				</div><div class="right">
					<?php echo date('l jS F Y');?>
				</div>
			</div>
		</div>
		<div class="logo"><a onclick="storePosition()" href="index.php"><img src="images/logo.png"></a></div>
		<?php
			// get list of tables and views to create arrays
			$result = mysqli_query($db, 'SHOW tables;');
			$tables = array();
			$tables_fk = array();
			while( $row = mysqli_fetch_row($result) )
			{
				$tablename = $row[0];
				if(substr($tablename, 0, 4) != 'view' && substr($tablename, 0, 4) != 'form' && substr($tablename, 0, 4) != 'list')
				{array_push( $tables, ucfirst($tablename) );}
				if(substr($tablename, 0, 4) == 'view' || substr($tablename, 0, 4) == 'form')
				{array_push( $tables_fk, ucfirst( substr($tablename, 4) ) );}
			}
			// create menu items containing table names
			echo '<div class="menu"><div class="separator"></div>';
			for($i = 0 ; $i < count($tables) ; $i++)
			{
				echo '<a onclick="storePosition()" href="index.php?table='.$tables[$i].'"><div class="menu_item';
				if (isset($_REQUEST['table']) && $_REQUEST['table'] == $tables[$i]) {echo '_selected';}
				echo '">'.$tables[$i].'</div></a><div class="separator"></div>';
			}
			echo '</div>';
		?>
		<div class="social">
			<a target="_blank" href="http://www.twitter.com"><img src="images/twitter.png"></a><br />
			<a target="_blank" href="http://www.instagram.com"><img src="images/instagram.png"></a><br />
			<a target="_blank" href="http://www.facebook.com"><img src="images/facebook.png"></a><br />
			<a target="_blank" href="http://www.youtube.com"><img src="images/youtube.png"></a><br />
			<a target="_blank" href="http://www.plus.google.com"><img src="images/googleplus.png"></a><br />
			<a target="_blank" href="http://www.linkedin.com"><img src="images/linkedin.png"></a><br />
		</div>
		<div class="footer">
			Copyright © 2018 -- iT-off -- All rights reserved
		</div>
	</body>
</html>