<!DOCTYPE html>
<html>
<head>
	<title>Quiz App</title>
	<link rel="stylesheet" href="styles.css"> 
</head>
<body>
	<?php
		require("conn.php");

		if(!empty($_POST['userEmail']))
		{
			authenticateUser();
		}
		else
		{
			// display a form to get user input
			getUserDetails();
		}

		function getUserDetails()
		{
			echo('
				<h1 align="center">Login Page</h1>
				<form method="POST" align="center">
					<table align="center">
						<tr><td>Email</td><td><input type="text" required minlength=3 name="userEmail"></td></tr>
						<tr><td><br></td></tr>
						<tr><td>Password</td><td><input type="password" required minlength=6 style=width:95%; name="userPassword"></td></tr>
						<tr></tr>
						<tr><td colspan="2"><input type="submit" value="Login"></td></tr>
					</table>		
				</form>
				');
		}

		function authenticateUser()
		{
			global $conn;

			$em = $_POST['userEmail'];
			$pw = $_POST['userPassword'];

			if(strpos($em, "@student"))
				$sql = "SELECT userPassword FROM student WHERE userEmail = '$em'";
			else
				$sql = "SELECT userPassword FROM staff WHERE userEmail = '$em'";

			if($result = mysqli_query($conn, $sql))
			{
				$inWhile = 0;
				while($row = mysqli_fetch_array($result))
				{
					$inWhile = 1;
					if(password_verify($pw, $row['userPassword']))
					{	
						$_SESSION['user']= $em;
						if(strpos($em, "@student"))
						{
							$_SESSION['isStudent'] = 1;
							echo "<script> window.location.assign('studentPage.php'); </script>";
						}

						else
						{
							$_SESSION['isStudent'] = 0;
							echo "<script> window.location.assign('staffPage.php'); </script>";
						}
					}
					else
					{
						echo "<script> alert('Incorrect email or password!');</script>";
						getUserDetails();
					}
					
				}
				if($inWhile == 0)
				{
					echo "<script> alert('Incorrect email or password!');</script>";
					getUserDetails();
				}
			
			}
			
		}

	?>
</body>

</html>