<!DOCTYPE html>
<html>
<head>
	<title>Quiz App</title>
</head>
<body>
	<?php
		$host = "localhost";
		$un = "root";
		$pw = "root";
		$db = "QuizApplication";

		$conn = mysqli_connect($host, $un, $pw, $db);

		if(!$conn)
		{
			die("Could not connect MySQL server" . mysqli_connect_error($conn));
		}
		else
		{
			echo("Connected to database server - All good!");
		}


		$sql = "CREATE DATABASE QuizApplication";

		if (mysqli_query($conn, $sql))
		{
			echo("Database created!");
		}
		else
		{
			echo("Database NOT created");
		}

		//STUDENT

		$sql = "CREATE TABLE if not exists student (
			userId int auto_increment primary key,
			userFirstname varchar(100) not null,
			userLastname varchar(100) not null,
			userEmail varchar(100) not null unique,
			userPassword varchar(255) not null
			)";
		mysqli_query($conn, $sql);

		// STAFF

		$sql = "CREATE TABLE if not exists staff (
			userId int auto_increment primary key,
			userFirstname varchar(100) not null,
			userLastname varchar(100) not null,
			userEmail varchar(100) not null unique,
			userPassword varchar(255) not null
			)";
		mysqli_query($conn, $sql);

		// QUIZ

		$sql = "CREATE TABLE if not exists quiz (
			staffId int,
			quizId int auto_increment,
			quizName varchar(100) not null,
			quizAuthor varchar(100) not null,
			quizDuration int not null,
			quizAvailability boolean not null,

			FOREIGN KEY(`staffId`) REFERENCES staff(`userId`)
			ON DELETE CASCADE,
			PRIMARY KEY(`quizId`)
			)";
		mysqli_query($conn, $sql);

		//QUESTION

		$sql = "CREATE TABLE if not exists question (
			questionId int auto_increment,
			quizId int,
			questionContent varchar(1000) not null,

			FOREIGN KEY(`quizId`) REFERENCES quiz(`quizId`)
			ON DELETE CASCADE,
			PRIMARY KEY(`questionId`)
			)";
		mysqli_query($conn, $sql);

		$sql = "CREATE TABLE if not exists answer (
			answerId int auto_increment,
			questionId int,
			answerContent varchar(1000) not null,
			isCorrect boolean not null,

			FOREIGN KEY(`questionId`) REFERENCES question(`questionId`)
			ON DELETE CASCADE,
			PRIMARY KEY(`answerId`)
			)";

		mysqli_query($conn, $sql);


		$sql = "CREATE TABLE if not exists student_score(
			scoreId int auto_increment,
			quizId int,
			userId int,
			result int not null,
			maximumResult int not null,
			dateOfAttempt timestamp not null,

			FOREIGN KEY(`quizId`) REFERENCES quiz(`quizId`)
			ON DELETE CASCADE,
			FOREIGN KEY(`userId`) REFERENCES student(`userId`)
			ON DELETE CASCADE,
			PRIMARY KEY(`scoreId`)
			)";


		
		if (mysqli_query($conn, $sql))
            {
                echo("Table Created!");
            }
            else
            {
                echo("Table NOT created:" . mysqli_error($conn));
            }

        $sql = "CREATE TABLE if not exists staff_score(
			scoreId int auto_increment,
			quizId int,
			userId int,
			result int not null,
			maximumResult int not null,
			dateOfAttempt timestamp not null,

			FOREIGN KEY(`quizId`) REFERENCES quiz(`quizId`)
			ON DELETE CASCADE,
			FOREIGN KEY(`userId`) REFERENCES staff(`userId`)
			ON DELETE CASCADE,
			PRIMARY KEY(`scoreId`)
			)";
		
		if (mysqli_query($conn, $sql))
            {
                echo("Table Created!");
            }
            else
            {
                echo("Table NOT created:" . mysqli_error($conn));
            }

	?>
</body>
</html>