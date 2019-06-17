<?php

session_start();
include 'conn1.php';
$idImg = $_SESSION['login_first_name'];

$file = $_FILES['file'];

$fileName = $file['name'];
$fileSize = $file['size'];
$fileError = $file['error'];
$fileTmpName = $file['tmp_name'];
$fileType = $file['type'];

$fileExt = explode('.',$fileName);
$fileActualExt = strtolower(end($fileExt)); 

$allowed = array("jpg","jpeg","png","pdf","PNG", "png");

if(in_array($fileActualExt, $allowed)){
	if($fileError === 0){
		if($fileSize < 150000){
				$fileNewName = "profileimg_".$idImg.".".$fileActualExt;
				$_SESSION['finalImgName'] = $fileNewName; 
				
				$fileDestination = "uploads2/".$fileNewName;
				move_uploaded_file($fileTmpName, $fileDestination);
				$sql = "UPDATE profileimg_learning SET status = 0 WHERE userid='$idImg';";
				mysqli_query($conn1, $sql);
				$sql2 = "UPDATE students SET profile_image = '$fileNewName' WHERE first_name='$idImg';";
				mysqli_query($conn1, $sql2);
				Header("Location: login_page.php?uploadImg=success");


		}else{
			echo "your file is too big";
		}

	}else{
		echo "error uploading this file";

	}

}else{
	echo "invalid extension";

}