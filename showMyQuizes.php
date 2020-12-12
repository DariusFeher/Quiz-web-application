<!DOCTYPE html>
<html>
<head>
	<title>Quiz App</title>
	<link rel="stylesheet" href="styles.css"> 
</head>
<body>
	<h1 align="center">My Quizes Page</h1> 
	<form method="POST" align="center" >
		<input type="submit" style="width:15%;" name="displayMyQuizesData" value="Display My Quizzes"/>
	</form>

	<?php
		require("conn.php");
		global $conn;

		$currentUserEmail = $_SESSION['user'];

		$sql = "SELECT userFirstname, userLastName, userId from staff WHERE userEmail = '$currentUserEmail' ";

		$result = mysqli_query($conn, $sql);
	
		while($row = mysqli_fetch_array($result))
		{
			$author = $row['userFirstname'] . " " . $row['userLastname'];
			$staffId = $row['userId'];
		}

		$sql = "SELECT quizId, quizName, quizDuration, quizAvailability from quiz WHERE staffId = '$staffId' ";
		$result = mysqli_query($conn, $sql);
		
		$quizIds = array();
		$quizNames = array();
		$quizDurations = array();
		$quizAvailability = array();

		while($row = mysqli_fetch_array($result))
		{
			array_push($quizIds, $row['quizId']);
			array_push($quizNames, $row['quizName']);
			array_push($quizDurations, $row['quizDuration']);
			array_push($quizAvailability, $row['quizAvailability']);
		}
		
		if(isset($_POST['displayMyQuizesData']))
		{
			echo("<p> &nbsp </p>");
			echo("<form method=POST align=center> 
					<table id=my_table style=width:60%;border-bottom-left-radius:0px;border-bottom-right-radius:0px; align=center>
						<tr><td><h2>Quiz Name</h2></td><td><h2> Duration</h2></td><td><h2>Availability</h2></td><td><h2>Edit Quiz</h2></td></tr>
				");

			for($index=0; $index<sizeof($quizIds); $index++)
			{
				if($index != sizeof($quizIds) -1)
				{
				echo("
					<form method=POST align=center> 
						<table id=my_table style=table-layout:fixed;width:60%;border-radius:0px; align=center>
							<tr><td><h3>". "$quizNames[$index]" . "</td><td>". "$quizDurations[$index]" . "</td><td>". "$quizAvailability[$index]" . "</td><td>". "<input type=submit name=openQuiz value='Open Quiz' />" . "</h3><input type=hidden name=quizIndex value=$index></td></tr>
						</table>
					</form>
					");
				}
				else
				{
					echo("
					<form method=POST align=center> 
						<table id=my_table style=table-layout:fixed;width:60%;border-top-left-radius:0px;border-top-right-radius:0px; align=center>
							<tr><td><h3>". "$quizNames[$index]" . "</td><td>". "$quizDurations[$index]" . "</td><td>". "$quizAvailability[$index]" . "</td><td>". "<input type=submit name=openQuiz value='Open Quiz' />" . "</h3><input type=hidden name=quizIndex value=$index></td></tr>
						</table>
					</form>
					");
				}
			}
			
		}

		// Opens the quiz
		if(isset($_POST['openQuiz']))
		{
			$_SESSION['no_quiz_opened'] = $_POST['quizIndex'];
			// $index = $_SESSION['no_quiz_opened'];
			// display_quiz($_SESSION['no_quiz_opened']);
			display_quiz_details($_SESSION['no_quiz_opened']);
			
		}

		// Opens the quiz which can be updated/edited
		if(isset($_POST['editQuiz']))
		{
			$_SESSION['deleteQuestion'] = 'NEW DELETION';
			$_SESSION['deleteAnswer'] = 'NEW DELETION';
			display_quiz($_SESSION['no_quiz_opened']);
		}


		if(isset($_POST['deleteQuiz']))
		{
			global $quizIds;
			$quizId = $quizIds[$_SESSION['no_quiz_opened']];
			$sql = "SELECT questionId FROM question WHERE quizId ='$quizId'";
			$result = mysqli_query($conn, $sql);

			$currentQuizQuestionsIds = array();

	
			while($row = mysqli_fetch_array($result))
			{
				array_push($currentQuizQuestionsIds, $row['questionId']);
			}

			$_SESSION['questionsIds'] = $currentQuizQuestionsIds;

			for($question_nr=0; $question_nr<sizeof($currentQuizQuestionsIds); $question_nr++)
			{

				$question_id_to_be_deleted = $currentQuizQuestionsIds[$question_nr];
				$sql = "DELETE FROM answer WHERE questionId ='$question_id_to_be_deleted'";
				mysqli_query($conn, $sql);
				$sql = "DELETE FROM question WHERE questionId ='$question_id_to_be_deleted'";
				mysqli_query($conn, $sql);
			}
			$sql = "DELETE FROM student_score WHERE quizId ='$quizId'";
				mysqli_query($conn, $sql);

			$sql = "DELETE FROM staff_score WHERE quizId ='$quizId'";
				mysqli_query($conn, $sql);
				
			$sql = "DELETE FROM quiz WHERE quizId ='$quizId'";
			if(mysqli_query($conn, $sql))
				echo "<script> alert('Quiz successfully deleted!');</script>";


		}

		if(isset($_POST['deleteQuestion']))
		{	
			if($_SESSION['deleteQuestion'] == 'NEW DELETION')
			{ 	
				$question_to_be_deleted = 0;
				for($q_nr=0; $q_nr < sizeof($_SESSION['questionsContent']); $q_nr ++)
					if($_POST['deleteQuestion'][$q_nr])
						$question_to_be_deleted = $q_nr;
				$question_id_to_be_deleted = $_SESSION['questionsIds'][$question_to_be_deleted];

				$sql = "DELETE FROM answer WHERE questionId ='$question_id_to_be_deleted'";
				mysqli_query($conn, $sql);
				
				$sql = "DELETE FROM question WHERE questionId ='$question_id_to_be_deleted'";
				if(mysqli_query($conn, $sql))
					echo "<script> alert('Question successfully deleted!');</script>";

				$_SESSION['deleteQuestion'] = 'OLD DELETION';
			}

			//display_quiz($_SESSION['no_quiz_opened']);
			display_quiz_details($_SESSION['no_quiz_opened']);
		}

		if(isset($_POST['submitChanges']))
		{
			submit_changes();
			display_quiz($_SESSION['no_quiz_opened']);

		}

		if(isset($_POST['deleteAnswer']))
		{
			if($_SESSION['deleteAnswer'] == 'NEW DELETION')
			{
				$q_nr_for_ans = 0;
				$ans_nr_to_be_deleted=0;
				for($q_nr=0; $q_nr < sizeof($_SESSION['questionsContent']); $q_nr ++)
				{	
					for($ans_nr=0; $ans_nr < $_SESSION['no_ans'][$q_nr]; $ans_nr++)
						{
							if(isset($_POST['deleteAnswer'][$q_nr][$ans_nr]))
								{
									$q_nr_for_ans = $q_nr;
									$ans_nr_to_be_deleted = $ans_nr;
								}
						}
				}
				$ans_id = $_SESSION['ans_id'][$q_nr_for_ans][$ans_nr_to_be_deleted];
				$sql = "DELETE FROM answer WHERE answerId='$ans_id'";
				if(mysqli_query($conn, $sql))
				{
					echo("<script> alert('Answer successfully deleted!');</script>");
				}
				$_SESSION['deleteAnswer'] = 'OLD DELETION';
			}
			submit_changes();
			display_quiz_details($_SESSION['no_quiz_opened']);
		}

		if(isset($_POST['addNewQuestion']))
		{
			display_quiz_with_add_new_question($_SESSION['no_quiz_opened']);
		}

		if(isset($_POST['createNewQuestion']))
		{
			submit_changes();
			$noOfAnswers = $_POST['noOfAnswers'];
			display_quiz_with_new_question_and_answers($_SESSION['no_quiz_opened'], $noOfAnswers);
		}

		if(isset($_POST['submitNewQuestionAndChanges']))
		{
			global $quizIds;
			$question_nr_to_be_added = sizeof($_SESSION['questionsContent']);
			
			$questionContent = $_POST['questionContent'][$question_nr_to_be_added];
			$quizId = $quizIds[$_SESSION['no_quiz_opened']]; 

			$sql = "INSERT INTO question(questionContent,
										quizId) VALUES
										('$questionContent',
										 '$quizId')";

			if(mysqli_query($conn, $sql))
			{
				$last_id = $conn->insert_id;
				$noOfAnswers = sizeof($_POST['ansContent'][$question_nr_to_be_added]);

				for($q_nr=0; $q_nr < $noOfAnswers; $q_nr ++)
				{
					$ansContent = $_POST['ansContent'][$question_nr_to_be_added][$q_nr];
					$isCorrect = $_POST['ansCorrect'][$question_nr_to_be_added][$q_nr];
					$sql = "INSERT INTO answer(answerContent,
												questionId,
												isCorrect)
												VALUES
												('$ansContent',
												 '$last_id',
												 '$isCorrect')";
					if(!mysqli_query($conn, $sql))
					{
						echo(mysqli_error($conn));
					}
				}
			}
			else
			{
				echo(mysqli_error($conn));
			}
			echo "<script> alert('Quiz successfully updated!');</script>";
			submit_changes();
			display_quiz_details($_SESSION['no_quiz_opened']);


		}
		function submit_changes(){
			global $quizIds;
			global $conn;

			$current_quiz_id = $quizIds[$_SESSION['no_quiz_opened']];
			$quizName = $_POST['quizName'];
			$quizDuration = $_POST['quizDuration'];
			$quizAvailable = $_POST['quizAvailable'];
			$sql = "UPDATE quiz
					SET quizName='$quizName', quizDuration='$quizDuration', quizAvailability='$quizAvailable'
					WHERE quizId='$current_quiz_id';";
			$result = mysqli_query($conn, $sql);
			// if($result)
			// {
			// 	echo('quiz details upd');
			// }
			// else
			// {
			// 	echo('quiz details not UPD' . mysqli_error($conn));
			// }
			for($q_nr=0; $q_nr<sizeof($_POST['questionContent']); $q_nr++)
			{
				for($ans_nr=0; $ans_nr<sizeof($_POST['ansContent'][$q_nr]); $ans_nr++)
				{
					$answerContent = $_POST['ansContent'][$q_nr][$ans_nr];
					$answerIsCorrect = $_POST['ansCorrect'][$q_nr][$ans_nr];
					$answerId = $_SESSION['ans_id'][$q_nr][$ans_nr];
					// print('ANSWER IS CORRECT  ' . $answerIsCorrect);
					$sql = "UPDATE answer
							SET answerContent='$answerContent', isCorrect='$answerIsCorrect'
							WHERE answerId='$answerId'";
					$result = mysqli_query($conn, $sql);

				}

				$questionContent = $_POST['questionContent'][$q_nr];
				$questionId = $_SESSION['questionsIds'][$q_nr];
				$sql = "UPDATE question
						SET questionContent='$questionContent'
						WHERE questionId='$questionId'";

				$result = mysqli_query($conn, $sql);
				// if($result)
				// {
				// 	echo('ans upd');
				// }
				// else
				// {
				// 	echo('ans not UPD' . mysqli_error($conn));
				// }
			}

			$currentUserEmail = $_SESSION['user'];

			$sql = "SELECT userId from staff WHERE userEmail = '$currentUserEmail' ";

			$result = mysqli_query($conn, $sql);
		
			while($row = mysqli_fetch_array($result))
			{
				$staffId = $row['userId'];
			}

			$sql = "SELECT quizId, quizName, quizDuration, quizAvailability from quiz WHERE staffId = '$staffId' ";
			$result = mysqli_query($conn, $sql);
			
			global $quizNames;
			global $quizIds;
			global $quizDurations;
			global $quizAvailability;

			$quizIds = array();
			$quizNames = array();
			$quizDurations = array();
			$quizAvailability = array();

			while($row = mysqli_fetch_array($result))
			{
				array_push($quizIds, $row['quizId']);
				array_push($quizNames, $row['quizName']);
				array_push($quizDurations, $row['quizDuration']);
				array_push($quizAvailability, $row['quizAvailability']);
			}
		}

		function display_quiz_with_new_question_and_answers($quiz_no, $noOfAnswers)
		{
			global $quizNames;
			global $quizIds;
			global $quizDurations;
			global $quizAvailability;
			global $conn;

			$index = $quiz_no;
			$quizName = $quizNames[$index];
			$quizId = $quizIds[$index];
			$quizDuration = $quizDurations[$index];
			$quizAvailable = $quizAvailability[$index];
			
			echo("
				<form method=POST align=center> 
					<table id=my_table style=width:60%; align=center>
					<tr><td colspan='3'><h2>Quiz Details</h2></td></tr>
					<tr><td><h3>Quiz Title</h3></td><td><input type=text style=width:100%; required minlength=1 placeholder='Your quiz name ...' name=quizName value='". $quizName . "'></td></tr>
					<tr><td><h3>Quiz Duration</h3></td><td><input type=number style=width:100%; required min=1 placeholder='Your quiz duration ...' name=quizDuration value=". $quizDuration . "></td></tr>
					");
			if($quizAvailable == 1)
			{
				echo("<tr><td><h3>Quiz Availability</h3></td><td><input type=radio required id=option1 name=quizAvailable value=1 checked>Yes
               		 <input type=radio id=option2 name=quizAvailable value=0>No". "</td></tr>
                ");
			}
			else
			{
				echo("<tr><td><h3>Quiz Availability</h3></td><td><input type=radio required id=option1 name=quizAvailable value=1>Yes
               		 <input type=radio id=option2 name=quizAvailable value=0 checked>No". "</td></tr>
                ");
			
			}

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
					<tr><td colspan='4'><hr style='height:1.5px;border-width:0;color:gray;background-color:#45a049;'></td></tr>
					<tr><td><h3>Question " . $current_q_nr. "</h3></td><td><input type=submit style='background-color: #F44336;' name=deleteQuestion[$q_index] value='Delete Question' /></td></tr>
					<tr><td><p>Question content</p></td><td><input type=text style=width:100%; required minlength=5 placeholder='Question content...' name=questionContent[$q_index] value='". $questionsContent[$q_index] . "'/></td><td>Correct Answer</td></tr>
					");

				$sql = "SELECT answerId, answerContent, isCorrect FROM answer WHERE questionId='$questionsId[$q_index]'";
				$result = mysqli_query($conn, $sql);
				
				$answersContent = array();
				$answersIsCorrect = array();
				$answerIds = array();
		
				while($row = mysqli_fetch_array($result))
				{
					array_push($answersContent, $row['answerContent']);
					array_push($answersIsCorrect, $row['isCorrect']);
					array_push($answerIds, $row['answerId']);
				}

				$_SESSION['no_ans'][$q_index] = sizeof($answersContent);
				// echo('no ans is ' . $_SESSION['no_ans'][$q_index]);

				for($ans_index=0; $ans_index < sizeof($answersContent); $ans_index ++)
				{	
					$_SESSION['ans_id'][$q_index][$ans_index] = $answerIds[$ans_index];
					$current_ans_index = $ans_index + 1;
					echo(
							"<tr><td>" . "Answer $current_ans_index" . "</td>" . "<td>" . "<input type=text required minlength=1 name=ansContent[$q_index][$ans_index] value='". $answersContent[$ans_index] . "'> ". "</td>"
						);

					if($answersIsCorrect[$ans_index] == 1)
					{
						echo("
							<td><input type=radio required name=ansCorrect[$q_index][$ans_index] value=1 checked>Yes
		               		 <input type=radio name=ansCorrect[$q_index][$ans_index] value=0 >No</td>
		                ");
					}
					else
					{
						echo("
							<td><input type=radio required name=ansCorrect[$q_index][$ans_index] value=1 >Yes
		               		 <input type=radio name=ansCorrect[$q_index][$ans_index] value=0 checked> No</td>
		                ");
					}
					echo("<td><input type=submit style='background-color: #F44336;' name=deleteAnswer[$q_index][$ans_index] value='Delete' /></td></tr>");
				}
			}
			echo("	
					<tr><td> &nbsp </td></tr>
					<tr><td colspan='4'><hr style='height:1.5px;border-width:0;color:gray;background-color:#45a049;'></td></tr>
					<tr><td><h3>New Question</h3></td></tr>
					<tr><td><p>Question content</p></td><td><input type=text style=width:100%; required minlength=5 placeholder='Question content...' name=questionContent[$q_index] ></td><td>Correct Answer</td></tr>
				");

			for($ans_index=0; $ans_index<$noOfAnswers; $ans_index++)
			{	
				$current_ans_index = $ans_index + 1;
				echo(
							"<tr><td>" . "Answer $current_ans_index" . "</td>" . "<td>" . "<input type=text required minlength=1 name=ansContent[$q_index][$ans_index] placeholder='Answer content ...'> ". "</td>"
						);

		
				echo("
					<td><input type=radio required name=ansCorrect[$q_index][$ans_index] value=1>Yes
               		 <input type=radio name=ansCorrect[$q_index][$ans_index] value=0 >No</td></tr>
                ");
					
					
			}


			echo("	
					<tr><td colspan='4'><input type=submit style='width:40%' name='submitNewQuestionAndChanges' value='Submit Changes'></td></tr>
					</table>
				</form>
				");
		}

		function display_quiz_with_add_new_question($quiz_no)
		{
			global $quizNames;
			global $quizIds;
			global $quizDurations;
			global $quizAvailability;
			global $conn;

			$index = $quiz_no;
			$quizName = $quizNames[$index];
			$quizId = $quizIds[$index];
			$quizDuration = $quizDurations[$index];
			$quizAvailable = $quizAvailability[$index];
			
			echo("
				<form method=POST align=center> 
					<table id=my_table style=width:60%; align=center>
					<tr><td colspan='3'><h2>Quiz Details</h2></td></tr>
					<tr><td><h3>Quiz Title</h3></td><td><input type=text style=width:100%; required minlength=1 placeholder='Your quiz name ...' name=quizName value='". $quizName . "'></td></tr>
					<tr><td><h3>Quiz Duration</h3></td><td><input type=number style=width:100%; required min=1 placeholder='Your quiz duration ...' name=quizDuration value=". $quizDuration . "></td></tr>
					");
			if($quizAvailable == 1)
			{
				echo("<tr><td><h3>Quiz Availability</h3></td><td><input type=radio required id=option1 name=quizAvailable value=1 checked>Yes
               		 <input type=radio id=option2 name=quizAvailable value=0>No". "</td></tr>
                ");
			}
			else
			{
				echo("<tr><td><h3>Quiz Availability</h3></td><td><input type=radio required id=option1 name=quizAvailable value=1>Yes
               		 <input type=radio id=option2 name=quizAvailable value=0 checked>No". "</td></tr>
                ");
			
			}

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
					<tr><td colspan='4'><hr style='height:1.5px;border-width:0;color:gray;background-color:#45a049;'></td></tr>
					<tr><td><h3>Question " . $current_q_nr. "</h3></td><td><input type=submit style='background-color: #F44336;' name=deleteQuestion[$q_index] value='Delete Question' /></td></tr>
					<tr><td><p>Question content</p></td><td><input type=text style=width:100%; required minlength=5 placeholder='Question content...' name=questionContent[$q_index] value='". $questionsContent[$q_index] . "'/></td><td>Correct Answer</td></tr>
					");

				$sql = "SELECT answerId, answerContent, isCorrect FROM answer WHERE questionId='$questionsId[$q_index]'";
				$result = mysqli_query($conn, $sql);
				

				$answersContent = array();
				$answersIsCorrect = array();
				$answerIds = array();
		
				while($row = mysqli_fetch_array($result))
				{
					array_push($answersContent, $row['answerContent']);
					array_push($answersIsCorrect, $row['isCorrect']);
					array_push($answerIds, $row['answerId']);
				}

				$_SESSION['no_ans'][$q_index] = sizeof($answersContent);

				for($ans_index=0; $ans_index < sizeof($answersContent); $ans_index ++)
				{	
					$_SESSION['ans_id'][$q_index][$ans_index] = $answerIds[$ans_index];
					$current_ans_index = $ans_index + 1;
					echo(
							"<tr><td>" . "Answer $current_ans_index" . "</td>" . "<td>" . "<input type=text required minlength=1 name=ansContent[$q_index][$ans_index] value='". $answersContent[$ans_index] . "'> ". "</td>"
						);

					if($answersIsCorrect[$ans_index] == 1)
					{
						echo("
							<td><input type=radio required name=ansCorrect[$q_index][$ans_index] value=1 checked>Yes
		               		 <input type=radio name=ansCorrect[$q_index][$ans_index] value=0 >No</td>
		                ");
					}
					else
					{
						echo("
							<td><input type=radio required name=ansCorrect[$q_index][$ans_index] value=1 >Yes
		               		 <input type=radio name=ansCorrect[$q_index][$ans_index] value=0 checked> No</td>
		                ");
					}
					echo("<td><input type=submit style='background-color: #F44336;' name=deleteAnswer[$q_index][$ans_index] value='Delete' /></td></tr>");
				}
			}
			echo("	
					<tr><td> &nbsp </td></tr>
					<tr><td colspan='4'><hr style='height:1.5px;border-width:0;color:gray;background-color:#45a049;'></td></tr>
					<tr><td colspan='4'><h3>New Question</h3></td></tr>
					<tr><td></td><td>Number of Answers   <input type=number required min=2 name=noOfAnswers></td><tr>
					<tr><td colspan='4'><input type=submit style='width:40%' name='createNewQuestion' value='Create New Question'></td></tr>
					</table>
				</form>
				");
		}
		function display_quiz($quiz_no){

			global $quizNames;
			global $quizIds;
			global $quizDurations;
			global $quizAvailability;
			global $conn;

			$index = $quiz_no;
			$quizName = $quizNames[$index];
			$quizId = $quizIds[$index];
			$quizDuration = $quizDurations[$index];
			$quizAvailable = $quizAvailability[$index];
			
			echo("
				<form method=POST align=center> 
					<table id=my_table style=width:60%; align=center>
					<tr><td colspan='3'><h2>Quiz Details</h2></td></tr>
					<tr><td><h3>Quiz Title</h3></td><td><input type=text style=width:100%; required minlength=1 placeholder='Your quiz name ...' name=quizName value='". $quizName . "'></td></tr>
					<tr><td><h3>Quiz Duration</h3></td><td><input type=number style=width:100%; required min=1 placeholder='Your quiz duration ...' name=quizDuration value=". $quizDuration . "></td></tr>
					");
			if($quizAvailable == 1)
			{
				echo("<tr><td><h3>Quiz Availability</h3></td><td><input type=radio required id=option1 name=quizAvailable value=1 checked>Yes
               		 <input type=radio id=option2 name=quizAvailable value=0>No". "</td></tr>
                ");
			}
			else
			{
				echo("<tr><td><h3>Quiz Availability</h3></td><td><input type=radio required id=option1 name=quizAvailable value=1>Yes
               		 <input type=radio id=option2 name=quizAvailable value=0 checked>No". "</td></tr>
                ");
			
			}

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
					<tr><td colspan='4'><hr style='height:1.5px;border-width:0;color:gray;background-color:#45a049;'></td></tr>
					<tr><td><h3>Question " . $current_q_nr. "</h3></td><td><input type=submit style='background-color: #F44336;' name=deleteQuestion[$q_index] value='Delete Question' /></td></tr>
					<tr><td><p>Question content</p></td><td><input type=text style=width:100%; required minlength=5 placeholder='Question content...' name=questionContent[$q_index] value='". $questionsContent[$q_index] . "'/></td><td>Correct Answer</td></tr>
					");

				$sql = "SELECT answerId, answerContent, isCorrect FROM answer WHERE questionId='$questionsId[$q_index]'";
				$result = mysqli_query($conn, $sql);
				

				$answersContent = array();
				$answersIsCorrect = array();
				$answerIds = array();
		
				while($row = mysqli_fetch_array($result))
				{
					array_push($answersContent, $row['answerContent']);
					array_push($answersIsCorrect, $row['isCorrect']);
					array_push($answerIds, $row['answerId']);
				}

				$_SESSION['no_ans'][$q_index] = sizeof($answersContent);

				for($ans_index=0; $ans_index < sizeof($answersContent); $ans_index ++)
				{	
					$_SESSION['ans_id'][$q_index][$ans_index] = $answerIds[$ans_index];
					$current_ans_index = $ans_index + 1;
					echo(
							"<tr><td>" . "Answer $current_ans_index" . "</td>" . "<td>" . "<input type=text required minlength=1 name=ansContent[$q_index][$ans_index] value='". $answersContent[$ans_index] . "'> ". "</td>"
						);

					if($answersIsCorrect[$ans_index] == 1)
					{
						echo("
							<td><input type=radio required name=ansCorrect[$q_index][$ans_index] value=1 checked>Yes
		               		 <input type=radio name=ansCorrect[$q_index][$ans_index] value=0 >No</td>
		                ");
					}
					else
					{
						echo("
							<td><input type=radio required name=ansCorrect[$q_index][$ans_index] value=1 >Yes
		               		 <input type=radio name=ansCorrect[$q_index][$ans_index] value=0 checked> No</td>
		                ");
					}
					echo("<td><input type=submit style='background-color: #F44336;' name=deleteAnswer[$q_index][$ans_index] value='Delete' /></td></tr>");

				}
			}
			echo("	
					<tr><td> &nbsp </td></tr>
					<tr><td colspan='4'><input type=submit style='width:40%' name=addNewQuestion value='Add New Question'/></td></tr>
					<tr><td colspan='4'><input type=submit style='width:40%' name=submitChanges value='Submit Changes'/></td></tr>
					</table>
				</form>
				");
		}

		function display_quiz_details($quiz_no)
		{
			global $quizNames;
			global $quizIds;
			global $quizDurations;
			global $quizAvailability;
			global $conn;

			$index = $quiz_no;
			$quizName = $quizNames[$index];
			$quizId = $quizIds[$index];
			$quizDuration = $quizDurations[$index];
			$quizAvailable = $quizAvailability[$index];
			
			echo("
				<form method=POST align=center> 
					<table id=my_table style=width:60%; align=center>
						<tr><td colspan='3'><h2>Quiz Details</h2></td></tr>
						<tr><td><h3 align='left'>Quiz Title</h3></td><td>$quizName</td></tr>
						<tr><td><h3 align='left'>Quiz Duration</h3></td><td>$quizDuration </td></tr>
					");

			if($quizAvailable == 1)
			{
				echo("<tr><td><h3 align='left'>Quiz Available</h3></td><td> YES </td></tr>");
			}
			else
			{
				echo("<tr><td><h3 align='left'>Quiz Available</h3></td><td> NO </td></tr>");
			
			}
			echo("<tr><td colspan='4'><hr style='height:1.5px;border-width:0;color:gray;background-color:#45a049;'></td></tr>");

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
					
					<tr><td><h3 align='left'>Question " . $current_q_nr. "</h3></td><td>
					<tr><td><p><b>Question content</b></p></td><td>" . $questionsContent[$q_index] . "</td><td><b>Correct Answer</b></td></tr>
					");

				$sql = "SELECT answerContent, isCorrect FROM answer WHERE questionId='$questionsId[$q_index]'";
				$result = mysqli_query($conn, $sql);
				

				$answersContent = array();
				$answersIsCorrect = array();
		
				while($row = mysqli_fetch_array($result))
				{
					array_push($answersContent, $row['answerContent']);
					array_push($answersIsCorrect, $row['isCorrect']);
				}
				for($ans_index=0; $ans_index < sizeof($answersContent); $ans_index ++)
				{	
					$current_ans_index = $ans_index + 1;
					echo(
							"<tr><td><b>" . "Answer $current_ans_index" . "</b></td>" . "<td>" . $answersContent[$ans_index] . "</td>"
						);

					if($answersIsCorrect[$ans_index] == 1)
					{
						echo("
							<td>YES</td></tr>
		                ");
					}
					else
					{
						echo("
							<td>NO</td></tr>
		                ");
					}
				}
				echo("<tr><td><br></br></td></tr>
					<tr><td colspan='4'><hr style='height:1.5px;border-width:0;color:gray;background-color:#45a049;'></td></tr>");
			}
			echo("
					<tr><td colspan='4'><input type=submit style='background-color: #F44336; width:40%' name=deleteQuiz value='Delete Quiz'/></td></tr>
					<tr><td colspan='4'><input type=submit style='background-color: #FFC107; width:40%'name=editQuiz value='Edit Quiz'/></td></tr>
					</table>
				</form>
				");
		}

		if(isset($_POST['goToMainPage']))
		{

			echo "<script> window.location.assign('staffPage.php'); </script>";
		}

	?>
	<form method="POST" align="center" >
		<input type="submit" style="width:15%;" name="goToMainPage" value="Main Page"/>
	</form>
</body>
</html>