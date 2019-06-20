<?php
/*თუ რამე კიდე ვერ გაიგეთ მომწერეთ, დამირეკეთ ან უბრალოდ google გამოიყენეთ(w3school, stack overflow, youtube, php ცნობარი,ჩვენი google doc-ები) ათასი საშუალება არსებობს*/
session_start();/*ეს კომანდი ვებ გვერდზე რთავს სესიას(სესია გამოიყენება იმისთვის რომ სხვადასხვა გვერდზე ხელმისაწვდომი იყოს ერთი და იმავე ინფორმაცია, მაგალითად: login input-ში რომ შეიყვანე შენი სახელი და პაროლი, სახელი მახსოვრდება სესიის გამოყენებით და როდესაც შენს აქაუნთზე გადახვალ იმ გვერდზეც შესაძლებელი იქნება რომ დალოგინების დროს შეყვანილი ინფო გამოიყენო */
include 'config.php';/*აქ ვაინქლუდებთ ფაილებს რომლებიც მონაცემთა ბაზასთან დასაკავშირებლად გვჭირდება,(conn1 ტყუილად დავაინქლუდე საჭირო არ იყო, უბრალოდ მერე აღარ მიცდია გამოსწორება როცა გავაკეთე), თუ ინქლუდი არ იცით, ეს საჭიროა ერთი ფაილის მეორეში გამოსაყენებლად, ანუ 1 ფაილის კოდის მეორე კოდში გამოსაყენებლად*/

include 'conn.php';
include 'conn1.php';


$empty_fields = false;
$success = false;

$invalid_symbols = false;
$invalid_email = false;
$taken = false;

$login_empty = false;
$login_invalid_name = false;
$login_invalid_password = false;/*აქ ვქმნით ყველა იმ ცვლადებს რომლებსაც ერორებისთვის გამოვიყენებთ, მაგალითად $empty_fields არის false მაგრამ კოდში გვიწერია რომ თუ რომელიმე გრაფა იქნება ცარიელი და ისე დავაჭერთ submit-ს მაშინ $empty_fields გაუტოლდეს true-ს და როდესაც ეს მოხდება კოდის სულ დაბლა გვიწერია if($empty_fields == true){მაშინ გამოაჩინოს წითელი ერორი;} */ 

$action = isset($_GET['action']) ? $_GET['action'] : '';/*როდესაც ვაჭერთ ღილაკ "Add Student"-s მაშინ საიტის URL-ში ჩნდება action=add, ხოლო $action ლინკიდან სწორედ ის ხდება რასაც action უდრის, ანუ "add"-s(დაბლა კოდში ნახეთ), ხოლო როდესაც $action გაუტოლდება add-s,დაბლა switch ფუნქციაში ამოქმედდება პირველი ნაწილი case 'add':-ის შემდეგ break;-ამდე*/
$inserted = isset($_GET['inserted']) ? $_GET['inserted'] : false;

switch ($action) {

	case 'add':

		  	

		$first_name = isset($_GET['first_name']) ? $_GET['first_name'] : '';/*ვთქვათ სახელის გრაფაში ჩავწერეთ "გოჩა", კოდში დაბლა გვიწერია რომ $_GET მეთოდით იქ შეყვანილი ინფორმაცია გამოაჩინოს URL-ში "first_name=გოჩა"-ს სახით ხოლო რასაც გაუტოლდება URL-ში first_name, ის ხდება $first-name, ანუ შეიყვანე "გოჩა", URL-ში გამოჩნდა first_name=გოჩა, $first_name-მა აიღო first_name-ის ღირებულება ლინკიდან და გახდა ტექსტი "გოჩა", */
		$last_name = isset($_GET['last_name']) ? $_GET['last_name'] : '';
		$password = isset($_GET['password']) ? $_GET['password'] : '';
		$email = isset($_GET['email']) ? $_GET['email'] : '';
		$mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';
		$gender = isset($_GET['gender']) ? $_GET['gender'] : '0';

		  $sql1 = "SELECT * FROM students WHERE first_name ='$first_name';";/*ეს sql-ის ბრძანება მონიშნავს მონაცემთა ბაზაში("students" თეიბლს)  ყველაფერს სადაც  first_name სიაში იპოვის იმას რაც შენ შეიყვანე საიტის მთავარ გვერდზე სახელის გრაფაში. ეს იმისთვისაა რომ შევამოწმოთ არსებობს თუ არა ეს სახელი უკვე მონაცემთა ბაზაში როდესაც დარეგისტრირებას ვცდილობთ. ვთქვათ მონაცემთა ბაზაში უკვე არის ვიღაცა სახელად გოჩა, როდესაც ჩვენ დარეგისტრირებას ვეცდებით გოჩას სახელით, მაშინ ეს ბრძანება "მონიშნავს"" მონაცემთა ბაზაში იმ ადგილს სადაც გოჩას იპოვის, ანუ $sql1="მონიშნოს ყველაფერი სტუდენტებიდან სადაც სახელი='ჩვენს_მიერ_შეყვანილ_სახელს';" მაგრამ ეს ბრძანება რომ გაუშვა mysqli_query($conn1, $sql1); ბრძანება დაგჭირდება, ფრჩხილებში პირველად conn1 იმიტომ მივუთითეთ რომ ჯერ მონაცემთა ბაზას დაუკავშირდეს და შემდეგ ამ მონაცემთა ბაზაში შეამოწმოს სახელები. */
		  $result = mysqli_query($conn1, $sql1);
		  $resultCheck = mysqli_num_rows($result);/*mysqli_num_rows ასე ვთქვათ ითვლის შედეგებს, ანუ როდესაც ჩვენ შევიყვანეთ გრაფაში გოჩა და $result = mysqli_query($conn1, $sql1);-ით მოინიშნა მონაცემთა ბაზაში უკვე ჩაწერილი გოჩა, ხოლო mysqli_num_rows($result)-ით ასე ვთქვათ დავითვალეთ $result-ის შედეგები, ანუ რა მონიშნა $result-მა ბაზაში,ანუ ამ შემთხვევაში გოჩა მართლა არსებობს ბაზაში, $result მონიშნავს მას, ხოლო $resultCheck დაითვლის მონიშნულების რაოდენობას, ანუ $resultCheck ნოლზე მეტი გახდება, დაბლა გვიწერია რომ როდესაც $resultCheck ხდება ნოლზე მეტი მაშინ $taken(ცვლადი რომელსაც ვიყენებთ ერორის გამოსაჩენად) ხდება true(ხოლო თუ true ხდება მაშინ ამოვარდება წითელი ერორი"Username is taken", ერორების გამოსაჩენად კოდი სულ დაბლა წერია)*/

		if(empty($first_name) ||empty($password)|| empty($last_name) || empty($email) || empty($mobile) || empty($gender)){
			/*ეს უბრალოდ ამოწმებს დატოვე თუ არა რომელიმე გრაფა ცარიელი    "||" ნიშნავს "ან"-ს, ესეიგი კოდში წერია რომ თუ სახელია ცარიელი "ან" პაროლი "ან" გვარი "ანმაილი "ან" მობილური და "ან" სქესი, მაშინ $empty_fields გახდეს true*/
			$empty_fields = true;
		}elseif(!preg_match("/^[a-zA-Z0-9]*$/", $first_name)){
			/*ეს ცოტა რთული და მრავალფეროცანი ფუნქციაა, მაგრამ შორს რო არ წავიდე პროსტა დავწერ რო იმისთვისაა რომ შეამოწმოს შეყვანილ სახელში "a"-დან "z"-მდე ან ნოლიდან ცხრამდე-ს გარდა თუ იპოვა სხვა სიმბოლო მაშინ $invalid_symbols გახდეს true და შემდეგ ამოაგდოს ერორი "enter valid symbols"(ეს და სხვა ერორებიც კოდის დაბლითა ნაწილშია)*/
        	$invalid_symbols = true;
		}elseif($resultCheck > 0){
			/*ამაზე მაღლა მიწერია*/
     		$taken = true;
    	}elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        	$invalid_email = true;		
        	/*ეს ფუნქცია ამოწმებს სწორ ფორმატში გაქვს თუ არა მაილი შეყვანილი, მაგრამ ამ შემთხვევაში წინ "!" მაქ გამოყენებული, იმიტომ რომ მინდა გავიგო რომ არასწორ ფორმატშია თუ არა მაილი შეყვანილი და თუ კი მაშინ $invalid_email გახდეს true...*/

		} else {
			/*თუ ჩვენმა შეყვანილმა input-მა ყველა ერორის ტესტი გაიარა და არაფერია შეცდომით მაშინ გაგრძელდება დაბლა ნაჩვენები კოდი*/


			$hashedpass = password_hash($password, PASSWORD_DEFAULT);
			/*password_hash ჩვენს მიერ შეყვანილ პაროლს ჰეშავს უსაფრთხოებისთვის*/

			$sql = "INSERT INTO students (first_name, last_name, password, email, mobile, gender_id) 
					VALUES(:first_name, :last_name, :password, :email, :mobile, :gender);";
					/*ვინც არ იცით ეს არის prepared statement, ანუ ცვლადების მონაცემთა ბაზაში უსაფრთხოდ შეტანა, ანუ ჯერ first_name-ის მაგივრად ბაზაში შეაქვს ":first_name" და შემდეგ ანაცვლებს მას $first_name-ით, იმით რაც ჩვენ შევიყვანეთ, ამაზე ლექციაზე გავაგრძელებ(თუ სურვილი გაქვთ SQL injection მოძებნეთ ინტერნეტში)*/
			$success = false;

			try {
				/*try{}catch(){} მარტივად რომ ვთქვა იგივე "if(){ }else{ }"-ია რასაც შეიტან try-ში ეცდება რომ შეასრულოს, თუ არა გადავა catch-ში რაც წერია იმაზე და შეასრულებს*/

				$stmt = $conn -> prepare($sql);
				$res = $stmt -> execute([
					'first_name' => $first_name, /*ეს არის ჩანაცვლების პროცესი*/
					'last_name' => $last_name, 
					'password' => $hashedpass,
					'email' => $email, 
					'mobile' => $mobile, 
					'gender' => $gender
				]);

				header('Location: index.php?inserted=true');/*წარმატებით შეყვანის შემდეგ header-ი გვაბრუნებს მთავარ გვერდზე(ისედაც ვიყავით მარა მაინც) და თან URL-ში უმატებს inserted=true, ხოლო თუ URL-ში მოიძებნა "inserted=true" მაშინ ამოგვიგდოს  "inserted successfully", ესეც დაბლა წერია*/
				

				$success = true;

			}
			catch(Exception $e) {
				/*ეს იმ შემთხვევაში თუ try-ში შეტანილი კოდი არ შესრულდება მოხდება catch-ში შეტანილი კოდი და ამოაგდებს ერორს თავისი მესიჯით და დაბლა წარწერით "database error!" */

				echo 'Exception -> ';
				var_dump($e->getMessage());

				?>
				<div class="alert alert-danger">
				  	<strong>database error!</strong>
				</div>
				<?php

			}
				 $sql2 = "INSERT INTO profileimg_learning (userid, status) VALUES(:userid, :status);";
				 /*ექაუნთებზე რომ ფოტოების ატვირთვისთვის შევქმენი ახალი Table სახელად profileimg_learning რომელშიც როდესაც რეგისტრირდები შედის 2 ინფორმაცია, userid-ში შედის შენი სახელი ხოლო სტატუსში - "1", რადგან login page-ის კოდში მიწერია რომ თუ შესული მომხმარებელის სტატუსი უდრის 1-ს მაშინ მისი პროფილი ფოტოდ დიფოლთი ფოტო გამოაჩინოს,ამაზეც მერე ვისაუბრებთ*/
		       try{
		          $stmt2 = $conn -> prepare($sql2);/*აქ ისევ prepared statement-ით უსაფრთხოდ შეგვაქ ეს ყველაფერი ბაზაში*/
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

		break;/*აქ მთავრდება ის კოდი რაც ხდება იმ შემთხვევაში თუ დავაჭერთ "Add Student"-ს */


	case 'delete':/*იმ შემთხვევაში თუ დავაჭეთ სახელის და გვარის გასწვრივ ნაგვის ურნას, მაშინ URL-ში გამოჩნდება action=delete, ხოლო $action ხდება "delete", შემოდის switch ფუნქციაში და ასრულებს შემდეგ კოდს. იღებს იმ სტუდენტის id-ს რომელსაც დააჭირე, შემდეგ $query="UPDATE students SET deleted = 1 WHERE id = " . $student_id;-ით ბაზაში deleted სვეტში შეყავს "1", ამას ვაკეთებთ იმიტომ რომ თუ ჩვენ დავაჭერთ რომელიმე მოსწავლის გასწვრივ ურნას, აიღოს მისი ID, შემდეგ მოძებნოს ბაზაში ეს მოსწავლე და deleted-ში ჩაუწეროს "1" რადგან შემდეგ საიტზე გამოვაჩინოთ მხოლოდ ის მოსწავლეები ვისაც deleted-ში 0 უწერიათ, ანუ თუ ბაზაში ვინმეს deleted-ში 0 უწერია ის გამოჩნდეს, 1 კი არა. ხოლო როცა მოსწავლის გვერდით ვაჭერთ ურნას მას ბაზაში 1 ეწერება deleted-ში*/

		$student_id = $_GET['student_id'];/*ამით URL-დან ვიღებთ იმ სტუდენტის ID-ს რომლის გასწვრივაც დავაჭირეთ ურნას,(როდესაც ურნას დავაჭირეთ URL-ში საიტს დაემატა action=delete?student_id="იმ_სტუდენტის_ID")*/

		
		$query = "UPDATE students SET deleted = 1 WHERE id = " . $student_id; 

		$stmt = $conn -> query($query);

		break;
	
	default:
		# code...
		break;
}


// select all users
/*ამით ყველა მოსწავლის სქესის ID-ს მივაბავთ ნამდვილ სქესზე, ჩვენ ხო მონაცემთა ბაზაში "კაცი"-ს და "ქალი"-ს მაგივრად 1 და 2 გვიწერია, მაგრამ გვაქვს მეორე table სადაც გვიწერია რომ ID 1 არის "კაცი" ხოლო, 2 - "ქალი", ჩვენ კიდე ვეუბნებით რომ სადაც ჩვენი სქესის ID ანუ 1 და 2, რომლებიც students თეიბლში გვიწერია მიაბას მეორე table-ზე იქ სადაც ჩვენი "gender_id" უდრის იმ თეიბლის "id"-ს, ანუ თუ შენ დარეგისტრირდი კაცად, ბაზაში სახელის გვარის და პაროლის გასწვრივ "gender_id"-ში იწერება 1, და რადგან მეორე თეიბლში გვაქ (id - 1 : კაცი, id - 2 : ქალი) ამ გადაბმით ეკრანზე გამოიტანს არა "1"-ს არამედ "კაცი"-ს*/
$query = "	SELECT 
				students.*,
					gender.name AS gender_name
			FROM students
			INNER JOIN gender ON gender.id = students.gender_id
			WHERE deleted = 0
			ORDER BY id DESC;";
$stmt = $conn -> query($query);

$students = $stmt -> fetchAll();
/*როცა ჩვენ "SELECT students.*" დავწერეთ მოვნიშნეთ ყველა სტუდენტის ყველა ინფო რომლებსაც deleted-ში 0 არ უწერიათ, ხოლო $students = $stmt->fetchAll;-ით ჩვენ ეს ყველა მონაცემები შევინახეთ $studentს-ში(ამას დაბლა გამოვიყენებთ დამიახსოვრე)*/

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
		if ($empty_fields) { /*აი ნანატრი "დაბლა" სადაც ერორები გამოგვაქ იმ შემთხვევაში თუ რაღაც ცვლადები true გახდება, იხურება php და ვწერთ მარტივ html-ს ერორის გამოსაჩენად, და მერე ისევ ვხსნით php-ს რომ eleif-ით გავაგრძელოთ სხვა ერორების შემოწმება, როდესაც არ უთითებ if($empty_fields == true){ #ერორის კოდი#} და წერ მარტო if($empty_fields){#ერორის კოდი#}
		დიფოლთად მაინც true-ს უდრის თუ არა მაგაზე ამოწმებს, ანუ თუ $empty_fields გახდება true მაშინ ამოქმედდება ფიგურულ ფრჩხილებში შეტანილი კოდი
		*/
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

		/*ამეებს ხვალე შევავსებ დღეს ვეღარ ვასწრებ,ესენი ექაუნთზე დალოგინებისთვისაა,უფრო სწორად დალოგინების ერორების კოდია, მაშინ სხვა გვერდებზეც მომიწევს შევსება, მოკლედ რომ ავღწერო ეს იღებს საიტის მთლიან URL-ს და ამოწმებს შიგნით ხომ არ მოიძებნება login=empty, და თუ მოიძებნა მაშინ $ogin_empty გახდეს true, და თუ თრუ გახდება ანალოგიურად როგორც მაღლა გავაკეთეთ იგივე მოხდეს, მაგრამ URL-ში ეს სიტყვები მხოლოდ მას შემდეგ შეიძლება მოხდეს რაც Login ღილაკს დააჭერთ და ამოქმედდება login.php ფაილი, სადაც შემოწმდება შეყვანილი დეტალები და თუ ცარიელია ან არასწორი სახელია ან რამე მერე header-ით გაბრუნებს index.php-ზე ოგღონფ URL-ში უმატებს login=empty ან login=invalidname*/
		$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		if(strpos($fullUrl, "login=empty")){
			$login_empty = true;
		}elseif(strpos($fullUrl, "login=invalidname") == true){
			$login_invalid_name = true;
		}elseif(strpos($fullUrl,"login=invalidpassword") == true){
			$login_invalid_password = true;
		}
		

		if($login_empty){/*აი აქ გამოაქ ერორები თუ დასალოგინებელ გრაფაში შეცდომები დაუშვი*/
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
		
		<form action="index.php" method="get"><!-- აი ეს არის დასარეგისტრირებელი ფორმა, როცა შეიყვან შენს დეტალებს და დააჭერ Add Students ან ენტერს, მაშინ განხორციელდება ის კოდი რაც action-ში გიწერია, ამ შემთხვევაში index.php, ანუ იგივე გვერდი სადაც ვართ,რადგან ამ გვერდის თავში გვიწერია შესაბამისი php კოდი, method=get-ით კიდე შეყვანილი დეტალები გადაქვაქ URL-ში,რომელსაც მერე ამოწმებს მაღლა php-ს კოდი და შესაბამის ერორებს აგდებს თუ საჭიროა, ან თუ წარმატებით დაწერე ყველაფერი სწორად მაშინ მონაცემთა ბაზაში ამატებს ახალ სტუდენტს და შემდეგ აჩენს ამ გვერდზეც ჩამონათვალში -->

			<div class="form-group">
				<label for="usr">First Name:</label>
				<input type="text" class="form-control" name="first_name" placeholder="fisrt name"><!-- რასაც შეიყვან სახელის მაგივრად ის ავა URL-ში ასე: "first_name=რაც_შეიყვანე_ის" -->
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

			<input type="hidden" name="action" value="add"><!--თუ ენტერს დააჭირე URL-ს გადაეცემა დამალული პარამეტრი action, რომელიც დაჭერის შემთხვევაში უდრის "add"-ს, ხოლო თუ add-s უდრის მაშინ მაღლა php კოდში აქტიურდება switch-ის პირველი დებულება, რომელიც ჯერ ამოწმებს და მერე შეავს ბაზაში დასარეგისტრირებელ გრაფაში ჩაწერილი ინფუთი -->

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
					/*აი ეს არის ის კოდი რითაც საიტზე ჩანან დამატებული მოსწავლეები, თუ არ გახსოვთ მაღლა ვთქვი რო $students-ში fetchAll()-ით შევინახეთ ყველა მოსწავლის ინფორმაცია, ახლა კი აქ foreach ციკლით გადავუარეთ ყველაფერს რაც $students-შია, თითოეულ წევრს $students-ში დავარქვით $student და შემდეგ თანმიმდევრობით echo თი გამოვიტანეთ მოსწავლეების ინფორმაცია ბაზიდან, echo $student['id'] ეკრანზე გამოიტანს ყველა სტუდენტის ID-ს,$student['first_name'] სახელს და ასე შემდეგ, სანამ ყველა წევრს არ გამოიტანს რაც fetchAll-ით $students-ში შევინახეთ */
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
					<?php /*აქ სურათის გარშემო დავწერეთ <a href="სადაც_გვინდა_რო_გადავიდეთ"> ფოტო </a>*  ჩვენ გვინდა რომ იგივე გვერდზე მოვხვდეთ მაგრამ URL-ში დაემატოს action=delete და student_id=იმ_სტუდენტის_ID_ვის_ურნასაც_დავაჭირეთ ანუ $student['id']*/
				}

				?>

			</tbody>
		</table>
	</div>

</body>
</html>


