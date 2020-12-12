<!DOCTYPE html>
<html>
<head>
	<title>Quiz App</title>
	<link rel="stylesheet" href="styles.css"> 
</head>
<body>
	<?php

		require("conn.php");
		global $conn;
		displayPageForm();
		

		function displayPageForm()
		{	

			echo('
				<h1 align="center">Landing Page</h1>
				<br></br>
				
				<form method="POST" align="center">
				<table style=width:20%; align="center">
						<tr><td><h3 align="center">Options</h3></td></tr>
						<tr><td><input type="submit" name="goToLogin" value="Login"/></td></tr>
						<tr><td><input type="submit" name="goToRegister" value="Register"/></td></tr>
					</table>
				</form>
				');

			if(isset($_POST['goToLogin'])) { 
	            echo "<script> window.location.assign('login.php'); </script>";
	        }

	        if(isset($_POST['goToRegister'])) { 
	            echo "<script> window.location.assign('register.php'); </script>";
	        } 
		}
	?>
</body>

</html>