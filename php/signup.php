<?php
	session_start();
	include("../config/connection.php"); 
	include("helperFunctionsDatabase.php");

	//submission of sign up form
	if(isset($_POST['submit'])){
		$firstname = $conn->real_escape_string($_POST["fname"]);
		$lastname = $conn->real_escape_string($_POST["lname"]);
		$password = $conn->real_escape_string($_POST["password"]);
		$email = $conn->real_escape_string($_POST["email"]);
		$user = $conn->real_escape_string($_POST["user"]);

		$firstname = testvalues($firstname);
		$lastname = testvalues($lastname);
		$password = testvalues($password);
		$email = testvalues($email);
		$user = testvalues($user);
        
        date_default_timezone_set("UTC");      
		$date = new DateTime('now');
        $date = $date->format('Y-m-d H:i:s');

		//determines usertype i.e. whether an admin or a user is being signed up
		if(isset($_SESSION["usertype"])){
			$usertype = "Admin";
		}
		else{
			$usertype = "User";
		}
		$passEncrypt= hash('ripemd160', $password);

		$insertuser = "CALL add_user('$user','$email', '$firstname', '$lastname','$date', '$passEncrypt', '$usertype')";
		queryInsert($conn, $insertuser);
		
		//if it is an admin who added the admin, they are returned to the admin page, otherwise the user is returned to the homepage
		if(isset($_SESSION["usertype"])){
			header( 'Location: ../admin.php');
		}
		else{
			header( 'Location: ../index.php');
		}
		
	}

	//checks to make sure email value is unique
	if(isset($_POST['checkemail'])){
		$email = $_POST['email']; 
		$sql = "select Email from users where Email = '$email'";

		$rs = query($conn, $sql);
	  

		if($rs->num_rows>0){  
	    
	    	echo 0;  
		}else{  
	      
	    	echo 1;  
		}  
	}

	//checks to make sure username value is unique
	if(isset($_POST['checkusername'])){
		$username = $_POST['username']; 
		$sql = "select username from users where username = '$username'";

		$rs = $conn->query($sql);
	  

		if($rs->num_rows>0){  
	    
	    	echo 0;  
		}else{  
	      
	   	   echo 1;  
		}  
	}
?>