<!DOCTYPE html>
<html>
<head>
	<title>Quiz App</title>
	<link rel="stylesheet" href="styles.css"> 
</head>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<style>
	
input[type=checkbox]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(2); /* IE */
  -moz-transform: scale(2); /* FF */
  -webkit-transform: scale(2); /* Safari and Chrome */
  -o-transform: scale(2); /* Opera */
  transform: scale(2);
  padding: 10px;
}

.checkboxtext
{
  /* Checkbox text */
  margin-left: 15px;
  font-size: 115%;
  display: inline;
}

</style>
<body>
	<h1 align="center">Quiz</h1>
	<!-- <i  class="fas fa-check-circle"  style="color: red;"></i> -->
	<?php
		require("conn.php");
		$quizNames = $_SESSION['quizNames'];
		$quizIds = $_SESSION['quizIds'];
		$quizAuthors = $_SESSION['quizAuthors'];
		$quizDurations = $_SESSION['quizDurations'];
		
		if($_SESSION['openedQuiz'] == 0)
		{
			display_quiz_questions($_SESSION['no_quiz_opened']);
			$_SESSION['openedQuiz'] = 1;
		}
		
		function display_submitted_quiz($quiz_no)
		{
			global $quizNames;
			global $quizIds;
			global $quizDurations;
			global $quizAuthors;
			global $conn;

			$index = $quiz_no;
			$quizName = $quizNames[$index];
			$quizId = $quizIds[$index];
			$quizDuration = $quizDurations[$index];
			$quizAuthor = $quizAuthors[$index];
			$score = $_SESSION['quizResult'];
			$maxScore = $_SESSION['quizMaximumResult'];
			echo("
				<form method=POST align=center> 
					<table id=my_table style=width:60%; align=center>
						<tr><td colspan='2'><h2>$quizName</h2></td></tr>
						<tr><td><br></td></tr>
						<tr><td><div align='left'><b style='font-size:20px; margin-right:15px;'> SCORE: </b> $score out of $maxScore  </div> </td></tr>
						<tr><td><br></td></tr>
						<tr><td><div align='left'><b style='font-size:20px; margin-right:15px;'> Quiz Duration:</b> $quizDuration min(s)</div> </td></tr>
						<tr><td><br></td></tr>
						<tr><td><div align='left'><b style='font-size:20px; margin-right:15px;'>Quiz Author:</b> $quizAuthor</div></td></tr>
					");

			echo("<tr><td colspan='2'><hr style='height:1.5px;border-width:0;color:gray;background-color:#45a049;'></td></tr>");

			$sql = "SELECT questionId, questionContent FROM question WHERE quizId = '$quizId'";

			$result = mysqli_query($conn, $sql);

			$questionsId = array();
			$questionsContent = array();

	
			while($row = mysqli_fetch_array($result))
			{
				array_push($questionsContent, $row['questionContent']);
				array_push($questionsId, $row['questionId']);
			}

			$_SESSION['questionsContent'] = $questionsContent;
			$_SESSION['questionsIds'] = $questionsId;

			for($q_index=0; $q_index<sizeof($questionsContent); $q_index++)
			{
				$current_q_nr = $q_index + 1;

				if($_SESSION['questionAnswered'][$q_index] == 1)
				{		
					echo("
						
						<tr><td><h2 align='left'>Question " . $current_q_nr. "</h2></td></tr>
						<tr><td><h3 align='left'>" . $questionsContent[$q_index] . "</h3></td></tr>
						");
				}
				else
				{
					echo("
						
						<tr><td><h2 align='left'>Question " . $current_q_nr. "<i  class='fas fa-times-circle'  style='color:red; margin-left:25px; font-size:15px;'> Not Answered!</i></h2></td></tr>
						<tr><td><h3 align='left'>" . $questionsContent[$q_index] . "</h3></td></tr>
						");
				}

				$sql = "SELECT answerContent, isCorrect FROM answer WHERE questionId='$questionsId[$q_index]'";
				$result = mysqli_query($conn, $sql);
				

				$answersContent = array();
				$answersIsCorrect = array();
		
				while($row = mysqli_fetch_array($result))
				{
					array_push($answersContent, $row['answerContent']);
					array_push($answersIsCorrect, $row['isCorrect']);
				}
				$_SESSION['answersContent'][$q_index] = $answersContent;
				$_SESSION['isAnswerCorrect'][$q_index] = $answersIsCorrect;

				
				for($ans_index=0; $ans_index < sizeof($answersContent); $ans_index ++)
				{	
					$current_ans_index = $ans_index + 1;
					if($_SESSION['ansSelected'][$q_index][$ans_index] == 1)
					{
						if($_SESSION['isAnswerCorrect'][$q_index][$ans_index] == 1)
						{
							echo(
							"
							<tr><td><div align='left'><i  class='fas fa-check-circle'  style='color: green; margin-right:15px'></i><input type=checkbox checked disabled name=checked[$q_index][$ans_index]><span class='checkboxtext'>" . $answersContent[$ans_index] . "</span></div><br></td>"
							);
						}
						else
						{
							echo(
							"
							<tr><td><div align='left'><i  class='fas fa-times-circle'  style='color: red; margin-right:15px'></i><input type=checkbox checked disabled name=checked[$q_index][$ans_index]><span class='checkboxtext'>" . $answersContent[$ans_index] . "</span></div><br></td>"
							);
						}
					}
					else
					{
						echo(
								"
								<tr><td><div align='left'><i  class='fas fa-times-circle'  style='color: red; margin-right:15px; visibility:hidden;'></i><input type=checkbox disabled><span class='checkboxtext'>" . $answersContent[$ans_index] . "</span></div><br></td>"
							);
					}
				}
				echo("<tr><td><br></br></td></tr>
					<tr><td colspan='2'><hr style='height:1.5px;border-width:0;color:gray;background-color:#45a049;'></td></tr>");
			}
			echo("
	
					<tr><td colspan='2'><input type=submit style='width:40%'name=goToQuizzesPage value='Quizzes Page'/></td></tr>
					</table>
				</form>
				");
		}

		function display_quiz_questions($quiz_no)
		{
			$_SESSION['submitted'] = 0;
			global $quizNames;
			global $quizIds;
			global $quizDurations;
			global $quizAuthors;
			global $conn;

			$index = $quiz_no;
			$quizName = $quizNames[$index];
			$quizId = $quizIds[$index];
			$quizDuration = $quizDurations[$index];
			$quizAuthor = $quizAuthors[$index];

			echo("
				<form method=POST align=center> 
					<table id=my_table style=width:60%; align=center>
						<tr><td colspan='2'><h2>$quizName</h2></td></tr>
						<tr><td><br></td></tr>
						<tr><td><div align='left'><b style='font-size:20px; margin-right:15px;'> Quiz Duration:</b> $quizDuration min(s)</div> </td></tr>
						<tr><td><br></td></tr>
						<tr><td><div align='left'><b style='font-size:20px; margin-right:15px;'>Quiz Author:</b> $quizAuthor</div></td></tr>
					");

			echo("<tr><td colspan='2'><hr style='height:1.5px;border-width:0;color:gray;background-color:#45a049;'></td></tr>");

			$sql = "SELECT questionId, questionContent FROM question WHERE quizId = '$quizId'";

			$result = mysqli_query($conn, $sql);

			$questionsId = array();
			$questionsContent = array();

	
			while($row = mysqli_fetch_array($result))
			{
				array_push($questionsContent, $row['questionContent']);
				array_push($questionsId, $row['questionId']);
			}

			$_SESSION['questionsContent'] = $questionsContent;
			$_SESSION['questionsIds'] = $questionsId;

			for($q_index=0; $q_index<sizeof($questionsContent); $q_index++)
			{
				$current_q_nr = $q_index + 1;

				echo("
					
					<tr><td><h2 align='left'>Question " . $current_q_nr. "</h2></td></tr>
					<tr><td><h3 align='left'>" . $questionsContent[$q_index] . "</h3></td></tr>
					");

				$sql = "SELECT answerId, answerContent, isCorrect FROM answer WHERE questionId='$questionsId[$q_index]'";
				$result = mysqli_query($conn, $sql);
				

				$answersContent = array();
				$answersIsCorrect = array();
				$answersIds = array();
		
				while($row = mysqli_fetch_array($result))
				{
					array_push($answersIds, $row['answerId']);
					array_push($answersContent, $row['answerContent']);
					array_push($answersIsCorrect, $row['isCorrect']);
				}

				$_SESSION['answersContent'][$q_index] = $answersContent;
				$_SESSION['isAnswerCorrect'][$q_index] = $answersIsCorrect;
				$_SESSION['answersIds'][$q_index] = $answersIds;

				for($ans_index=0; $ans_index < sizeof($answersContent); $ans_index ++)
				{	
					$current_ans_index = $ans_index + 1;
					echo(
							"
							<tr><td><div align='left'><input type=checkbox name=checked[$q_index][$ans_index]><span class='checkboxtext'>" . $answersContent[$ans_index] . "</span></div><br></td>"
						);
				}
				echo("<tr><td><br></br></td></tr>
					<tr><td colspan='2'><hr style='height:1.5px;border-width:0;color:gray;background-color:#45a049;'></td></tr>");
			}
			echo("
	
					<tr><td colspan='2'><input type=submit style='width:40%'name=submitAnswers value='Submit Answers'/></td></tr>
					</table>
				</form>
				");
		}

		if(isset($_POST['goToMainPage']))
		{
			if($_SESSION['isStudent'] == 1)
				echo "<script> window.location.assign('studentPage.php'); </script>";
			else
				echo "<script> window.location.assign('staffPage.php'); </script>";
		}

		if(isset($_POST['goToQuizzesPage']))
		{
			unset($_SESSION['openedQuiz']);
			unset($_SESSION['questionsContent']);
			unset($_SESSION['answersContent']);
			unset($_SESSION['ansSelected']);
			unset($_SESSION['quizResult']);
			unset($_SESSION['quizMaximumResult']);
			unset($_SESSION['isAnswerCorrect']);
			unset($_SESSION['questionAnswered']);
			echo "<script> window.location.assign('solveAvailableQuizzes.php'); </script>";
		}

		if(isset($_POST['submitAnswers']))
		{
			if($_SESSION['submitted'] == 0)
			{
			global $questionsContent;
			global $questionsId;
			global $conn;
			$no_questions = sizeof($_SESSION['questionsContent']);

			$result = 0;

			for($q_nr=0; $q_nr < $no_questions; $q_nr++)
			{	
				$no_answers = sizeof($_SESSION['answersContent'][$q_nr]);
				$no_correct_ans_selected = 0;
				$no_total_of_correct_answers = 0;
				$no_incorrect_ans_selected = 0;

				$_SESSION['questionAnswered'][$q_nr] = 0;

				for($ans_nr=0; $ans_nr < $no_answers; $ans_nr++)
				{
					
					

					$ansId = $_SESSION['answersIds'][$q_nr][$ans_nr];
					$_SESSION['ansSelected'][$q_nr][$ans_nr] = 0;
					if(isset($_POST['checked'][$q_nr][$ans_nr]))
					{	
						$_SESSION['questionAnswered'][$q_nr] = 1;
						$_SESSION['ansSelected'][$q_nr][$ans_nr] = 1;
						
						if($_SESSION['isAnswerCorrect'][$q_nr][$ans_nr])
						{
							$no_total_of_correct_answers ++;
							$no_correct_ans_selected ++;
						}
						else
						{
							$no_incorrect_ans_selected++;
						}
					}
				}
				$no_total_of_incorrect_answers = $no_answers - $no_total_of_correct_answers;
				$current_score = 0;
				if($no_total_of_correct_answers)
					$current_score = ($no_correct_ans_selected / $no_total_of_correct_answers);
				if($no_total_of_incorrect_answers)
					$current_score = $current_score - ($no_incorrect_ans_selected / $no_total_of_incorrect_answers);
				
				if($current_score > 0)
					$result += $current_score;
			}

			$_SESSION['quizResult'] = $result;
			$_SESSION['quizMaximumResult'] = $no_questions;


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

			$quizId = $quizIds[$_SESSION['no_quiz_opened']];

			// set isStudent
			$isStudent = $_SESSION['isStudent'];

			$maximumResult = $no_questions;

			// Insert into score table
			if($_SESSION['isStudent'] == 1)
			{
				$sql = "INSERT INTO student_score(
													  quizId,
													  userId,
													  result,
													  maximumResult)
													  VALUES (
													  		 '$quizId', '$userId', '$result', '$maximumResult')";
			}
			else
			{
				$sql = "INSERT INTO staff_score(
													  quizId,
													  userId,
													  result,
													  maximumResult)
													  VALUES (
													  		 '$quizId', '$userId', '$result', '$maximumResult')";
			}

			mysqli_query($conn, $sql);

			$_SESSION['openedQuiz'] = 1;
			$_SESSION['submitted'] = 1;		
			}
			display_submitted_quiz($_SESSION['no_quiz_opened']);
		}

	?>
	<form method="POST" align="center" >
		<br>
		<input type='submit' style=width:15%; name='goToMainPage' value='Main Page'/>
	</form>
						
</body>
</html>