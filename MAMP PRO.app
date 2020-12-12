<!DOCTYPE html>
<html>
<head>
	<title>Create Database</title>
</head>
<body>
	<?php
		$host = "localhost";
		$un = "root";
		$pw = "root";

		$conn = mysqli_connect($host, $un, $pw);

		if(!$conn)
		{
			echo("dd")
			die("Could not connect MySQL server" . mysqli_connect_error($conn));
		}
		else
		{
			echo("Connected to database server - All good!");
		}


	?>
</body>
</html>