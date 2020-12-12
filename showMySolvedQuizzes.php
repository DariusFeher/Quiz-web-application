<!DOCTYPE html>
<html>
<head>
	<title>Quiz App</title>
	<link rel="stylesheet" href="styles.css"> 
</head>
<body>
	<h1 align="center">Results Page</h1>
	<?php
		require("conn.php");
		global $conn;
		global $quizNames;
		global $quizAuthors;
		global $quizIds;
		global $dateOfAttempt;
		global $results;
		global $maximumResults;

		// get user id
		$em = $_SESSION['user'];
		
		if($_SESSION['isStudent'] == 1)
			$sql = "SELECT userId FROM student WHERE userEmail='$em'";
		else
			$sql = "SELECT userId FROM staff WHERE userEmail='$em'";

		
		$query_result = mysqli_query($conn, $sql);
		
		$userId = 0;
		while($row = mysqli_fetch_array($query_result))
		{
			$userId = $row['userId'];
		}

		if($_SESSION['isStudent'] == 1)
			$sql = "SELECT quizId, dateOfAttempt, result, maximumResult FROM student_score WHERE userId='$userId'";
		else
			$sql = "SELECT quizId, dateOfAttempt, result, maximumResult FROM staff_score WHERE userId='$userId'";


		
		$query_result = mysqli_query($conn, $sql);
		
		$userId = 0;

		$quizIds = array();
		$dateOfAttempt = array();
		$results = array();
		$maximumResults = array();

		while($row = mysqli_fetch_array($query_result))
		{
			array_push($quizIds, $row['quizId']);
			array_push($dateOfAttempt, $row['dateOfAttempt']);
			array_push($results, $row['result']);
			array_push($maximumResults, $row['maximumResult']);
		}

		$quizNames = array();
		$quizAuthors = array();

		for($quiz_nr=0; $quiz_nr<sizeof($quizIds); $quiz_nr++)
		{
			$sql = "SELECT quizName, quizAuthor FROM quiz WHERE quizId = '$quizIds[$quiz_nr]'";

			$query_result = mysqli_query($conn, $sql);
			while($row = mysqli_fetch_array($query_result))
			{
				array_push($quizAuthors, $row['quizAuthor']);
				array_push($quizNames, $row['quizName']);
			}

		}
		
		display_solved_quizzes();

		function display_solved_quizzes()
		{
			global $quizNames;
			global $quizAuthors;
			global $quizIds;
			global $dateOfAttempt;
			global $results;
			global $maximumResults;

			echo("<form method=POST align=center> 
					<table id=my_table style=table-layout:fixed;width:60%;border-bottom-left-radius:0px;border-bottom-right-radius:0px; align=center>
						<tr><td><h2>Quiz Name</h2></td><td><h2>Score</h2></td><td><h2> Percentage (%)</h2></td><td><h2>Author</h2></td><td><h2>Date of Attempt</h2></td></tr>
				");

			for($index=0; $index<sizeof($quizIds); $index++)
			{	
				if($maximumResults[$index])
					$percentage = round(($results[$index] * 100 / $maximumResults[$index]), 2);
				if($index != sizeof($quizIds) -1)
				{
				echo("
					<form method=POST align=center>
						<table id=my_table style=table-layout:fixed;width:60%;border-radius:0px; align=center>
							<tr><td><h3>". "$quizNames[$index]" . "</td><td>". "$results[$index]" . "</td><td>". $percentage . "</td><td>". $quizAuthors[$index] . "</h3></td><td>$dateOfAttempt[$index]</td></tr>
						</table>
					</form>
					");
				}
				else
				{
					echo("
					<form method=POST align=center> 
						<table id=my_table style=table-layout:fixed;width:60%;border-top-left-radius:0px;border-top-right-radius:0px; align=center>
		
							<tr><td><h3>". "$quizNames[$index]" . "</td><td>". "$results[$index]" . "</td><td>". $percentage . "</td><td>". $quizAuthors[$index] . "</h3></td><td>$dateOfAttempt[$index]</td></tr>
							<tr><td colspan='5'><input type=submit name=goToMainPage style=width:40%; value='Main Page'></td><tr>
						</table>
					</form>
					<p> &nbsp </p>

					");
				}
			}
		}

		if(isset($_POST['goToMainPage']))
		{
			if($_SESSION['isStudent'] == 1)
				echo "<script> window.location.assign('studentPage.php'); </script>";
			else
				echo "<script> window.location.assign('staffPage.php'); </script>";

		}

		?>
</body>

</html>