<!DOCTYPE html>
<html>
<head>
	<title>Quiz App</title>
	<link rel="stylesheet" href="styles.css"> 
</head>
<body>
	<h1 align="center">Quizzes Page</h1>
	<?php
		require("conn.php");
		global $conn;

		$quizAv = 1;
		$sql = "SELECT * from quiz WHERE quizAvailability = 1";

		$result = mysqli_query($conn, $sql);
		
		$quizIds = array();
		$quizAuthors = array();
		$quizDurations = array();
		$quizNames = array();

		while($row = mysqli_fetch_array($result))
		{
			array_push($quizIds, $row['quizId']);
			array_push($quizAuthors, $row['quizAuthor']);
			array_push($quizDurations, $row['quizDuration']);
			array_push($quizNames, $row['quizName']);
		}
		
		display_available_quizzes();

		function display_available_quizzes()
		{
			global $quizIds;
			global $quizNames;
			global $quizDurations;
			global $quizAuthors;

			echo("<form method=POST align=center> 
					<table id=my_table style=table-layout:fixed;width:50%;border-bottom-left-radius:0px;border-bottom-right-radius:0px; align=center>
						<tr><td><h2>Quiz Name</h2></td><td><h2> Duration (min)</h2></td><td><h2>Author</h2></td><td><h2>Solve Quiz</h2></td></tr>
				");

			for($index=0; $index<sizeof($quizIds); $index++)
			{
				if($index != sizeof($quizIds) -1)
				{
				echo("
					<form method=POST align=center>
						<table id=my_table style=table-layout:fixed;width:50%;border-radius:0px; align=center>
							<tr><td><h3>". "$quizNames[$index]" . "</td><td>". "$quizDurations[$index]" . "</td><td>". "$quizAuthors[$index]" . "</td><td>". "<input type=submit name=openQuiz value='Open Quiz' />" . "</h3><input type=hidden name=quizIndex value=$index></td></tr>
						</table>
					</form>
					");
				}
				else
				{
					echo("
					<form method=POST align=center> 
						<table id=my_table style=table-layout:fixed;width:50%;border-top-left-radius:0px;border-top-right-radius:0px; align=center>
							<tr><td><h3>". "$quizNames[$index]" . "</td><td>". "$quizDurations[$index]" . "</td><td>". "$quizAuthors[$index]" . "</td><td>". "<input type=submit name=openQuiz value='Open Quiz' />" . "</h3><input type=hidden name=quizIndex value=$index></td></tr>
							<tr><td><br></td></tr>
							<tr><td colspan='4'><input type=submit name=goToMainPage style=width:40%; value='Main Page'></td><tr>
						</table>
					</form>
					<p> &nbsp </p>

					");
				}
			}
		}

		if(isset($_POST['openQuiz']))
		{
			global $quizIds;
			global $quizNames;
			global $quizDurations;
			global $quizAuthors;
			$_SESSION['no_quiz_opened'] = $_POST['quizIndex'];
			$_SESSION['quizNames'] = $quizNames;
			$_SESSION['quizIds'] = $quizIds;
			$_SESSION['quizAuthors'] = $quizAuthors;
			$_SESSION['quizDurations'] = $quizDurations;
			$_SESSION['openedQuiz'] = 0;
			echo "<script> window.location.assign('solveQuiz.php'); </script>";
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