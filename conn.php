<?php
		session_start();
		$host = "localhost";
		$un = "root";
		$pw = "root";
		$db = "QuizApplication";
		
		$conn = mysqli_connect($host, $un, $pw, $db);
		
?>