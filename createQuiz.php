<!DOCTYPE html>
<html>
<head>
	<title>Quiz App</title>
	<link rel="stylesheet" href="styles.css"> 
</head>
<body>


	<h1 align="center">Create Quiz</h1>
	<form method="POST" align="center">
		<table id="my_table" align="center" >
			<tr><td><h3 align="left"><b>Title</b></h3></td><td><input type="text"  placeholder="Your quiz title..." name="quizTitle" required minlength="2"  value="<?php if(isset($_POST['quizTitle'])) echo $_POST['quizTitle']; else echo $_SESSION['current_quiz']['quizName'];?>"></td></tr>
			<!-- <tr><td> Number of Questions</td><td><input type="number" name="noQuestions" value="<?php echo $_POST['noQuestions'];?>"></td><tr> -->
			 <tr><td><h3 align="left"><b>Duration</b></h3></td><td><input type="number" style="width:95%;" placeholder="Your quiz duration..." name="quizDuration" required min="1" value="<?php if(isset($_POST['quizDuration'])) echo $_POST['quizDuration'];?>"></td></td></tr>
            <tr><td><h3 align="left"><b>Available</b> </h3></td><td>
                <input type="radio" required id="option1" name="quizAvailable" value="true" <?php if (isset($_POST['quizAvailable']) && $_POST['quizAvailable']=="true") echo "checked";?> >Yes  
                <input type="radio" id="option2" name="quizAvailable" value="false" <?php if (isset($_POST['quizAvailable']) && $_POST['quizAvailable']=="false") echo "checked";?> >No</td></tr>
			<tr><td colspan="2"><input type="submit" class="myButtonClass" name="createQuiz" value="Create quiz questions" /></td></tr>
		</table>
	</form>
		<!-- onclick="this.disabled=true;" -->
		<!-- onclick="setInput(this); return false;"  -->
	
	
	<?php

		require("conn.php");
		global $conn;
		$last_id = 0;
		$index = 1;

		
		if(isset($_POST['goToStaffPage']))
		{
			echo "<script> window.location.assign('staffPage.php'); </script>";
		}

		if(isset($_POST['createQuiz']))
		{	
			$_SESSION['current_quiz']['question_number'] = 0;
			$quizTitle = $_POST['quizTitle'];
			$quizDuration = $_POST['quizDuration'];
			
			if($_POST['quizAvailable'] == "true")
				$quizAvailability = 1;
			else
				$quizAvailability = 0;

			$_SESSION['current_quiz']['quizName'] = $quizTitle;
			$_SESSION['current_quiz']['quizDuration'] = $quizDuration;
			$_SESSION['current_quiz']['quizAvailable'] = $quizAvailability;
			
			echo("<p> &nbsp </p>");
			$dom = new DOMDocument('1.0');//Create new document
			
			//Add the form
			$form = $dom->createElement('form');
			$dom->appendChild($form);
			$formAttribute = $dom->createAttribute('method');
			$formAttribute->value = 'POST';
			$form->appendChild($formAttribute);
			$formAttribute = $dom->createAttribute('align');
			$formAttribute->value = 'center';
			$form->appendChild($formAttribute);

			//Add the table
			$table = $dom->createElement('table');
			$tableAttribute = $dom->createAttribute('id');
			$tableAttribute->value = 'my_table';
			$table->appendChild($tableAttribute);
			$tableAttribute = $dom->createAttribute('align');
			$tableAttribute->value = 'center';
			$table->appendChild($tableAttribute);



			//Add new row
			$tr = $dom->createElement('tr');
			$table->appendChild($tr);

			//Add new column
			$th = $dom->createElement('th', 'New Question');
			$tr->appendChild($th);
			$thAttribute = $dom->createAttribute('colspan');
			$thAttribute->value = '2';
			$th->appendChild($thAttribute);

			//Add new row
			$tr = $dom->createElement('tr');
			$table->appendChild($tr);

			//Add new column
			$td = $dom->createElement('td', 'Number of Answers');
			$tr->appendChild($td);

			//Add new column
			$td = $dom->createElement('td');
			$tr->appendChild($td);

			//Add input element to column
			$input = $dom->createElement('input');
			$td->appendChild($input);
			$tdAttribute = $dom->createAttribute('type');
			$tdAttribute->value = 'number';
			$input->appendChild($tdAttribute);
			$tdAttribute = $dom->createAttribute('name');
			$tdAttribute->value = 'noAnswers';
			$input->appendChild($tdAttribute);
			$tdAttribute = $dom->createAttribute('min');
			$tdAttribute->value = '2';
			$input->appendChild($tdAttribute);


			//Add new row
			$tr = $dom->createElement('tr');
			$table->appendChild($tr);

			//Add new column
			$td = $dom->createElement('td');

			$tr->appendChild($td);

			$tdAttribute = $dom->createAttribute('colspan');
			$tdAttribute->value = '2';
			$td->appendChild($tdAttribute);


			//Add input element to column
			$input = $dom->createElement('input');
			$td->appendChild($input);
			$tdAttribute = $dom->createAttribute('type');
			$tdAttribute->value = 'submit';
			$input->appendChild($tdAttribute);
			$tdAttribute = $dom->createAttribute('class');
			$tdAttribute->value = 'myButtonClass';
			$input->appendChild($tdAttribute);
			$tdAttribute = $dom->createAttribute('name');
			$tdAttribute->value = 'createQuestionWithAnswers';
			$input->appendChild($tdAttribute);
			$tdAttribute = $dom->createAttribute('value');
			$tdAttribute->value = 'Create Question';
			$input->appendChild($tdAttribute);
			

			//Add table to form
			$form->appendChild($table);

			echo $dom->saveXml();

		}


		if(isset($_POST['createQuestionWithAnswers']) && $_SERVER['REQUEST_METHOD'] == 'POST')
		{
 			$_SESSION['questionStatus']= "New Question";
			$question_nr = $_SESSION['current_quiz']['question_number'] + 1;
			echo "<h3 align=center>" . "Question ". $question_nr . "</h3>";
			echo('
				<form method="POST" align="center"> 
					<table id="my_table" align="center">
						<tr><td colspan="3"></td></tr>
						<tr><td>Enter Question</td><td><input required minlength="5" type="text" name="questionContent" placeholder="Question content..."> </td><td>Correct Answer </td></tr>
						<tr><td><br></td></tr>
	
				');

			for($index=1; $index <= $_POST['noAnswers']; $index ++)
			{	
				echo(
						
						"<tr>" . "<td>" . "Answer $index" . "</td>" . "<td>" . "<input type=text required minlength=1 name=answer[] placeholder='Answer $index content...'>" . "</td>". "<td>". "<input type=radio required name=$index value=true >Yes"."</input>"."<input type=radio name=$index value=false >No"."</input>". "</td>" ."</tr>
						<tr><td><br></td></tr>"
					);
			}
			echo('
				<tr><td colspan="3"><input type="submit" style=width:50%; name="addQuestionToQuiz" value="Add Question to Quiz"></td></tr>
					</table>
				</form>
				');
			
		}

		if(isset($_POST['addQuestionToQuiz']) && $_SERVER['REQUEST_METHOD'] == 'POST')
		{
			if($_SESSION['questionStatus'] == "New Question")
			{
				if($_SESSION['current_quiz']['question_number'] == 0)
				{
					$quizTitle = $_SESSION['current_quiz']['quizName'];
					$quizDuration = $_SESSION['current_quiz']['quizDuration'];
					$quizAvailability = $_SESSION['current_quiz']['quizAvailable'];

					$currentUserEmail = $_SESSION['user'];

					$sql = "SELECT userFirstname, userLastName, userId from staff WHERE userEmail = '$currentUserEmail' ";

					$result = mysqli_query($conn, $sql);
					

					while($row = mysqli_fetch_array($result))
					{
						$author = $row['userFirstname'] . " " . $row['userLastname'];
						$staffId = $row['userId'];
					}
				
					$sql = "INSERT INTO quiz (	
												staffId,
												quizName,
												quizAuthor,
												quizDuration,
												quizAvailability) VALUES (

													'$staffId', '$quizTitle', '$author', '$quizDuration', '$quizAvailability')";
					
					if(mysqli_query($conn, $sql))
					{	
						$_SESSION['current_quiz']['quizName'] = $quizTitle;
						$_SESSION['current_quiz']['quizAuthor'] = $author;
						$_SESSION['current_quiz']['quizDuration'] = $quizDuration;
						$_SESSION['current_quiz']['quizAvailable'] = $quizAvailability;
						$_SESSION["quiz_created"] = "True";

						$last_id = $conn->insert_id;
						$_SESSION['last_quiz_id'] = $last_id;
					}
					else
					{
						echo("Something went wrong " . mysqli_error($conn));
					}			
				}

				$quizId = $_SESSION['last_quiz_id'];
				$questionContent = $_POST['questionContent'];

				$sql = "INSERT INTO question (	
												quizId,
												questionContent) VALUES (
													'$quizId', '$questionContent')";
				if(mysqli_query($conn, $sql))
				{
					$last_id = $conn->insert_id;
					$_SESSION['last_question_id'] = $last_id;
					$_SESSION['current_quiz']['question_number'] = $_SESSION['current_quiz']['question_number'] + 1;
				}
				else
				{
					echo("Something went wrong " . mysqli_error($conn));
				}

				for($index=0; $index < count($_POST['answer']); $index ++)
				{
					$answerContent = $_POST['answer'][$index];
					$questionId = $_SESSION['last_question_id'];
					if($_POST[$index+1] == "true")
						$correctAnswer = 1;
					else
						$correctAnswer = 0;

					$sql = "INSERT INTO answer (	
												questionId,
												answerContent,
												isCorrect) VALUES (
													'$questionId', '$answerContent', '$correctAnswer')";
					if(mysqli_query($conn, $sql))
					{
						$_SESSION['questionStatus']="Question and Answers Added";
					}
					else
					{
						echo("Something went wrong " . mysqli_error($conn));
					}
				}
			}

			echo('
				<p> &nbsp </p>
				<form method="POST" align="center" >
					<input type="submit" style="width:15%;" name="addNewQuestion" value="Add New Question"/>
				</form>
			');
		}
		if(isset($_POST['addNewQuestion']))
		{
			$dom = new DOMDocument('1.0');//Create new document
			echo("<p> &nbsp </p>");
			//Add the form
			$form = $dom->createElement('form');
			$dom->appendChild($form);
			$formAttribute = $dom->createAttribute('method');
			$formAttribute->value = 'POST';
			$form->appendChild($formAttribute);
			$formAttribute = $dom->createAttribute('align');
			$formAttribute->value = 'center';
			$form->appendChild($formAttribute);

			//Add the table
			$table = $dom->createElement('table');
			$tableAttribute = $dom->createAttribute('id');
			$tableAttribute->value = 'my_table';
			$table->appendChild($tableAttribute);
			$tableAttribute = $dom->createAttribute('align');
			$tableAttribute->value = 'center';
			$table->appendChild($tableAttribute);



			//Add new row
			$tr = $dom->createElement('tr');
			$table->appendChild($tr);

			//Add new column
			$th = $dom->createElement('th', 'New Question');
			$tr->appendChild($th);
			$thAttribute = $dom->createAttribute('colspan');
			$thAttribute->value = '2';
			$th->appendChild($thAttribute);

			//Add new row
			$tr = $dom->createElement('tr');
			$table->appendChild($tr);

			//Add new column
			$td = $dom->createElement('td', 'Number of Answers');
			$tr->appendChild($td);

			//Add new column
			$td = $dom->createElement('td');
			$tr->appendChild($td);

			//Add input element to column
			$input = $dom->createElement('input');
			$td->appendChild($input);
			$tdAttribute = $dom->createAttribute('required');
			$input->appendChild($tdAttribute);
			$tdAttribute = $dom->createAttribute('min');
			$tdAttribute->value = '2';
			$input->appendChild($tdAttribute);
			$tdAttribute = $dom->createAttribute('type');
			$tdAttribute->value = 'number';
			$input->appendChild($tdAttribute);
			$tdAttribute = $dom->createAttribute('name');
			$tdAttribute->value = 'noAnswers';
			$input->appendChild($tdAttribute);
			


			//Add new row
			$tr = $dom->createElement('tr');
			$table->appendChild($tr);

			//Add new column
			$td = $dom->createElement('td');

			$tr->appendChild($td);

			$tdAttribute = $dom->createAttribute('colspan');
			$tdAttribute->value = '2';
			$td->appendChild($tdAttribute);


			//Add input element to column
			$input = $dom->createElement('input');
			$td->appendChild($input);
			$tdAttribute = $dom->createAttribute('type');
			$tdAttribute->value = 'submit';
			$input->appendChild($tdAttribute);
			$tdAttribute = $dom->createAttribute('class');
			$tdAttribute->value = 'myButtonClass';
			$input->appendChild($tdAttribute);
			$tdAttribute = $dom->createAttribute('name');
			$tdAttribute->value = 'createQuestionWithAnswers';
			$input->appendChild($tdAttribute);
			$tdAttribute = $dom->createAttribute('value');
			$tdAttribute->value = 'Create Question';
			$input->appendChild($tdAttribute);
			


			//Add table to form
			$form->appendChild($table);

			echo $dom->saveXml();
		}

	?>
<!--  <script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script> -->
	<form method="POST" align="center" >
		<br>
		<input type="submit" style="width:15%;" name="goToStaffPage" value="Staff Page"/>
	</form>
</body>
</html>
