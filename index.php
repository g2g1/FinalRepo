<?php
session_start();
include 'config.php';
include 'conn.php';
include 'conn1.php';


$empty_fields = false;
$success = false;

$invalid_symbols = false;
$invalid_email = false;
$taken = false;

$login_empty = false;
$login_invalid_name = false;
$login_invalid_password = false;

$action = isset($_GET['action']) ? $_GET['action'] : '';
$inserted = isset($_GET['inserted']) ? $_GET['inserted'] : false;

switch ($action) {

	case 'add':

		  	

		$first_name = isset($_GET['first_name']) ? $_GET['first_name'] : '';
		$last_name = isset($_GET['last_name']) ? $_GET['last_name'] : '';
		$password = isset($_GET['password']) ? $_GET['password'] : '';
		$email = isset($_GET['email']) ? $_GET['email'] : '';
		$mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';
		$gender = isset($_GET['gender']) ? $_GET['gender'] : '0';

		  $sql1 = "SELECT * FROM students WHERE first_name ='$first_name';";
		  $result = mysqli_query($conn1, $sql1);
		  $resultCheck = mysqli_num_rows($result);

		if(empty($first_name) ||empty($password)|| empty($last_name) || empty($email) || empty($mobile) || empty($gender)){
			
			$empty_fields = true;
		}elseif(!preg_match("/^[a-zA-Z0-9]*$/", $first_name)){
        	$invalid_symbols = true;
		}elseif($resultCheck > 0){
     		$taken = true;
    	}elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        	$invalid_email = true;		

		} else {



			$hashedpass = password_hash($password, PASSWORD_DEFAULT);

			$sql = "INSERT INTO students (first_name, last_name, password, email, mobile, gender_id) 
					VALUES(:first_name, :last_name, :password, :email, :mobile, :gender);";

			$success = false;

			try {

				$stmt = $conn -> prepare($sql);
				$res = $stmt -> execute([
					'first_name' => $first_name, 
					'last_name' => $last_name, 
					'password' => $hashedpass,
					'email' => $email, 
					'mobile' => $mobile, 
					'gender' => $gender
				]);

				header('Location: index.php?inserted=true');
				

				$success = true;

			}
			catch(Exception $e) {

				echo 'Exception -> ';
				var_dump($e->getMessage());

				?>
				<div class="alert alert-danger">
				  	<strong>database error!</strong>
				</div>
				<?php

			}
				 $sql2 = "INSERT INTO profileimg_learning (userid, status) VALUES(:userid, :status);";

		       try{
		          $stmt2 = $conn -> prepare($sql2);
		          $res2 = $stmt2 -> execute([
		            'userid' => $first_name,
		            'status' => 1

		          ]);

		       }catch(Exception $e){
		        echo 'Exception ->';
		        var_dump($e->getMessage());

		        ?> <div class="alert alert-danger">
		              <strong>database error!</strong>
		            </div>
		            <?php

		       }
			

		}

		break;


	case 'delete':

		$student_id = $_GET['student_id'];

		// $query = "DELETE FROM students WHERE id = " . $student_id;
		$query = "UPDATE students SET deleted = 1 WHERE id = " . $student_id;

		$stmt = $conn -> query($query);

		break;
	
	default:
		# code...
		break;
}


// select all users
$query = "	SELECT 
				students.*,
					gender.name AS gender_name
			FROM students
			INNER JOIN gender ON gender.id = students.gender_id
			WHERE deleted = 0
			ORDER BY id DESC;";
$stmt = $conn -> query($query);

$students = $stmt -> fetchAll();

// echo '<pre>';
// print_r($_GET);
// echo '</pre>';


?><!DOCTYPE html>
<html lang="en">
<head>
	<title>Bootstrap Example</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
<body>


<div class="topnav">
  <form action="login.php" method="post">
            <input type="text" name="login_first" placeholder="username">
            <input type="text" name="login_pass" placeholder="password">
            <button type="submit" name="submit_login">Login</button>


          </form>
</div>
	
	<div class="container">
		<h2>Add New Student</h2>

		<?php
		if ($empty_fields) {
			?>
			<div class="alert alert-danger">
	 		 	<strong>Fill all required fields!</strong> 
			</div>
			<?php
		}elseif($invalid_symbols){
			?><div class="alert alert-danger">
                        <strong>Invalid characters!</strong> 
                      </div> <?php
         }elseif($invalid_email){            
         		?><div class="alert alert-danger">
                        <strong>invalid email!</strong> 
                      </div><?php


			 }else if ($inserted) {
			?>
			<div class="alert alert-success">
			  	<strong>Inserted Successfully!</strong>
			</div>
			<?php
		}


		$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		if(strpos($fullUrl, "login=empty")){
			$login_empty = true;
		}elseif(strpos($fullUrl, "login=invalidname") == true){
			$login_invalid_name = true;
		}elseif(strpos($fullUrl,"login=invalidpassword") == true){
			$login_invalid_password = true;
		}
		

		if($login_empty){
          ?><div class="alert alert-danger">
              <strong>Fill all required fields!</strong> 
            </div> <?php
      }elseif($login_invalid_name){
                    ?><div class="alert alert-danger">
                        <strong>Invalid name!</strong> 
                      </div> <?php

      }elseif($login_invalid_password){                
          ?><div class="alert alert-danger">
                        <strong>invalid password!</strong> 
                      </div><?php
                    }
                    ?>
		
		<form action="index.php" method="get">

			<div class="form-group">
				<label for="usr">First Name:</label>
				<input type="text" class="form-control" name="first_name" placeholder="fisrt name">
			</div>
			

			<div class="form-group">
				<label for="usr">Last Name:</label>
				<input type="text" class="form-control" name="last_name" placeholder="last name">
			</div>
			<div class="form-group">
				<label for="usr">Password:</label>
				<input type="password" class="form-control" name="password" placeholder="password">
			</div>


			<div class="form-group">
				<label for="email">Email:</label>
				<input type="text" class="form-control" name="email" id="email" placeholder="Email">
			</div>

			<div class="form-group">
				<label for="usr">Mobile:</label>
				<input type="text" class="form-control" name="mobile" placeholder="mobile">
			</div>

			<div class="form-group">
				<label for="gender">Gender:</label>
				<select class="form-control" name="gender">
					<option value="1">male</option>
					<option value="2">female</option>
				</select>
			</div> 

			 <button type="submit" class="btn">Add Student</button>

			<input type="hidden" name="action" value="add">

		</form>



		<h2>Students List</h2>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Student ID</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Email</th>
					<th>Mobile</th>
					<th>Birth Date</th>
					<th>Gender</th>
					<th>DEL</th>
				</tr>
			</thead>
			<tbody>

				<?php

				foreach ($students as $student) {
					?>
					<tr>
					<td><?php echo $student['id'] ?></td>
					<td><?php echo $student['first_name'] ?></td>
					<td><?=$student['last_name']?></td>
					<td><?=$student['email']?></td>
					<td><?=$student['mobile']?></td>
					<td><?=$student['birth_date']?></td>
					<td><?=$student['gender_name']?></td>
					<td>
						<a href="?action=delete&student_id=<?=$student['id']?>">
							<img style="width: 25px; cursor: pointer;" src="icons/delete.png">
						</a>
					</td>
				</tr>
					<?php
				}

				?>

			</tbody>
		</table>
	</div>

</body>
</html>


