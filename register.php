<!DOCTYPE html>
<html>
<head>
	<title>Quiz App</title>
	<link rel="stylesheet" href="styles.css"> 
</head>
<body>
	<?php

		require("conn.php");

		if(!empty($_POST['userEmail']) and !empty($_POST['userFirstname'])and !empty($_POST['userPassword']))
		{
			// add user to database
			addUserDetails();
		}
		else
		{
			// display a form to get user input
			getUserDetails();
		}

		function getUserDetails()
		{
			echo('
				<h2 align="center">Registration Page</h2>	
				<form method="POST" align="center" action=""> 
					<table align="center">
						<tr><td>First name</td><td><input type="text" required minlength="2" name="userFirstname"></td></tr>
						<tr><td><br></td></tr>
						<tr><td>Last name</td><td><input type="text"required minlength="2" name="userLastname"></td></tr>
						<tr><td><br></td></tr>
						<tr><td>Email</td><td><input type="text" required minlength="5" name="userEmail"></td></tr>
						<tr><td><br></td></tr>
						<tr><td>Password</td><td><input type="password" required minlength="6" name="userPassword"></td></td></tr>
						<tr><td><br></td></tr>
						<tr><td colspan="2"><input type="submit" value="Register"></td></tr>
					</table>
					
				</form>
				');
		}

		function addUserDetails()
		{
			global $conn;

			$fn = $_POST['userFirstname'];
			$ln = $_POST['userLastname'];
			$em = $_POST['userEmail'];
			$pw = $_POST['userPassword'];

			$pw = password_hash($pw, PASSWORD_DEFAULT);

			if (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
		     	echo "<script> alert('Invalid email format!');</script>";
		     	getUserDetails();
		    }
		    else
		    {
		    	$invalid = 0;
		    	$sql = "SELECT userEmail FROM student WHERE userEmail='$em'";
		    	if($result = mysqli_query($conn, $sql))
		    	{
		    		$emailIn = "";
		    		while($row = mysqli_fetch_array($result))
		    		{
		    			$emailIn = $row['userEmail'];
		    		}

		    		if($emailIn == $em)
	    			{
	    				$invalid = 1;
	    				echo "<script> alert('Email address is already used!');</script>";
	    				getUserDetails();
	    			}
		    	}
		    	$sql = "SELECT userEmail FROM staff WHERE userEmail='$em'";
		    	if($result = mysqli_query($conn, $sql))
		    	{
		    		$emailIn = "";
		    		while($row = mysqli_fetch_array($result))
		    		{
		    			$emailIn = $row['userEmail'];
		    		}

		    		if($emailIn == $em)
	    			{
	    				$invalid = 1;
	    				echo "<script> alert('Email address is already used!');</script>";
	    				getUserDetails();
	    			}
		    	}

		    	if($invalid == 0)
		    	{
					if(strpos($em, "@student"))
						$sql = "INSERT INTO student(userFirstname,
													userLastname,
													userEmail,
													userPassword) VALUES (

														'$fn', '$ln', '$em', '$pw')";
					else
						$sql = "INSERT INTO staff(	userFirstname,
													userLastname,
													userEmail,
													userPassword) VALUES (

														'$fn', '$ln', '$em', '$pw')";
						
					if(mysqli_query($conn, $sql))
					{
						echo "<script> window.location.assign('login.php'); </script>";
					}
					else
					{
						echo("Something went wrong..." . mysqli_error($conn));
					}
				}
			}
		}

		function test_input($data) {
		  $data = trim($data);
		  $data = stripslashes($data);
		  $data = htmlspecialchars($data);
		  return $data;
		}

	?>


</body>

</html>
<style>
.error {color: #FF0000;}
</style>