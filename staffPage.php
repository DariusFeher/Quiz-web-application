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
				<h1 align="center">Staff Page</h1>
				
				<form method="POST" align="center">
				<table style=width:25%; align="center">
						<tr><td><h3 align="center">Options</h3></td></tr>
						<tr><td><input type="submit" name="createQuiz" value="Create Quiz"/></td></tr>
						<tr><td><input type="submit" name="showMyQuizes" value="Show My Quizzes"/></td></tr>
						<tr><td><input type="submit" name="solveQuizzes" value="Solve Quizzes"/></td></tr>
						<tr><td><input type="submit" name="showMySubmittedQuizzes" value="Show Solved Quizzes"/></td></tr>
						<tr><td><input type="submit" name="logout" value="Logout"/></td></tr>
					</table>
				</form>
				');

			if(isset($_POST['createQuiz'])) { 
				$_SESSION['quiz_created'] = False;
	            echo "<script> window.location.assign('createQuiz.php'); </script>";
	        }

	        if(isset($_POST['showMyQuizes'])) { 
	            echo "<script> window.location.assign('showMyQuizes.php'); </script>";
	        }

	        if(isset($_POST['solveQuizzes'])) { 
	            echo "<script> window.location.assign('solveAvailableQuizzes.php'); </script>";
	        }

	        if(isset($_POST['logout']))
	        {	
	        	unset($_SESSION['user']);
	        	echo "<script> window.location.assign('index.php'); </script>";
	        } 

	        if(isset($_POST['showMySubmittedQuizzes']))
	        {	
	        	echo "<script> window.location.assign('showMySolvedQuizzes.php'); </script>";
	        } 
		}

	?>
</body>

</html>