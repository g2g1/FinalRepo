<?php
session_start();
include 'conn1.php';


if(isset($_POST['submit_login'])){
	$login_first_name = mysqli_real_escape_string($conn1,$_POST['login_first']);
	$login_password = mysqli_real_escape_string($conn1,$_POST['login_pass']);

	$sql = "SELECT * FROM students WHERE first_name = '$login_first_name';";
	$result = mysqli_query($conn1, $sql);
	$resultCheck = mysqli_num_rows($result);



	if(empty($login_first_name) || empty($login_password)){
		Header("Location: index.php?login=empty");

	}elseif($resultCheck < 1){
		Header("Location: index.php?login=invalidname");

	}elseif($resultCheck > 0){
		while($rows = mysqli_fetch_assoc($result)){
			$password_from_db = $rows['password'];
			
			$password_verify = password_verify($login_password, $password_from_db);
			
			if($password_verify == false){
				
				Header("Location: index.php?login=invalidpassword");
			}elseif($password_verify == true){
				$_SESSION['login_first_name'] = $login_first_name;
				$_SESSION['login_id'] = $rows['id'];
				Header("Location: login_page.php?login=success");

			}
		}

	}



}













?>