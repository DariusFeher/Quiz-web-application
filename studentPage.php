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
		getUserDetails();
		
		function getUserDetails()
		{	

			echo('
				<h1 align="center">Student Page</h1>
				
				<form method="POST" align="center">
				<table style=width:20%; align="center">
						<tr><td><h3 align="center">Options</h3></td></tr>
						<tr><td><input type="submit" name="solveQuizzes" value="Solve Quizzes"/></td></tr>
						<tr><td><input type="submit" name="showSolvedQuizzes" value="Show Solved Quizzes"/></td></tr>
						<tr><td><input type="submit" name="logout" value="Logout"/></td></tr>
					</table>
				</form>
				');

			if(isset($_POST['solveQuizzes'])) { 
	            echo "<script> window.location.assign('solveAvailableQuizzes.php'); </script>";
	        }
	        if(isset($_POST['showSolvedQuizzes'])) { 
	            echo "<script> window.location.assign('showMySolvedQuizzes.php'); </script>";
	        } 
	        if(isset($_POST['logout']))
	        {	
	        	unset($_SESSION['user']);
	        	echo "<script> window.location.assign('index.php'); </script>";
	        } 
		}

	?>
</body>

</html>