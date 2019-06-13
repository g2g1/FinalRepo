<?php
session_start();
include 'conn1.php';


?>
<form action="upload2.php" method="post" enctype="multipart/form-data">
	<input type="file" name="file">
	<button type="submit" name="submit_upload">Upload</button>

</form>
<?php 
$id_login = $_SESSION['login_id'];

$first = $_SESSION['login_first_name'];
$sql = "SELECT * FROM students WHERE first_name ='$first';";
$result = mysqli_query($conn1, $sql);
$rows1 = mysqli_fetch_assoc($result);
$resultCheck = mysqli_num_rows($result);
$finalImgName = $_SESSION['finalImgName'];
if($resultCheck > 0){
	$sql1 = "SELECT * FROM profileimg_learning WHERE userid='$first';";
	$resultImg = mysqli_query($conn1, $sql1);
		while($rows = mysqli_fetch_assoc($resultImg)){
			/*echo "<div>";
			if($rows['status'] == 1){
				echo "<img src='uploads2/default_profile.jpg'>";

			}elseif($rows['status'] == 0){
				$finalImgName = $_SESSION['finalImgName'];
				echo "<img src='uploads2/".$finalImgName."'>";
				
				

			}
				echo "</div>";*/
				?><div class="media">
  						<div class="media-left media-top"><?php
  						 if($rows['status'] == 1){
    					    ?><img src="uploads2/default_profile.jpg" class="media-object" style="width:180px">
    											<?php	}elseif($rows['status'] == 0){
    														echo "<img src='uploads2/".$finalImgName."' class='media-object' style='width:180px'>"; 
    																					}
    														?>
  						</div>
  							<div class="media-body">
    							<h4 class="media-heading">First Name: <?php echo $_SESSION['login_first_name']."<br>"; ?> Last Name: <?php  echo $rows1['last_name']; ?></h4>
    							<p><form action="login_page.php" method="post">
    									<input type="text" name="info" placeholder="info">
    									<button type="submit" name="submit_info">Submit</button>
    									<br>
    									<?php 
    										if(isset($_POST['info'])){
    											$info = $_POST['info'];
    											echo $info;
    										}
    									?>

    							</form></p>
 						    </div>
					</div><?php

		}


}


?>