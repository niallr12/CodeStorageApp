<?php
	include("../config/connection.php"); 
	include("helperFunctionsDatabase.php");
	session_start();
	//log in form submitted
	if(isset($_POST['submit'])){
		$username = $_POST["user"];
		$password = $_POST["password"];
		$passEncrypt= hash('ripemd160', $password);
		
		$checkuser = "SELECT * FROM users WHERE UserName = '$username'";
		$userrs = query($conn, $checkuser);

		//checks to see if it is a registered username
		if($userrs->num_rows != 1){
			$_SESSION["Login.Error"] = "That is not a registered username.";
			header( 'Location: ../index.php');
			exit();
		}
		$loggedin = "SELECT LoggedIn FROM users WHERE UserName = '$username'";
		$loggedinrs = query($conn, $loggedin);
		$loggedinrs->data_seek(0);
		$row = $loggedinrs->fetch_assoc();
		$login = $row['LoggedIn'];
		//checks to see if account is already logged on
		if($login == 1){
			$_SESSION["Login.Error"] = "This account is already logged in";
			header( 'Location: ../index.php');
			exit();
		}




		$sql = "SELECT * FROM users WHERE username='$username' AND password = '$passEncrypt'";

		$sqlselect = "SELECT LoginAttempts FROM users WHERE UserName = '$username'";
		$rs = query($conn, $sqlselect);
		$rs->data_seek(0); 
		$row = $rs->fetch_assoc();
		$_SESSION['loggedintries'] = $row['LoginAttempts'];

		//makes sures user has loginattempts left
		if($_SESSION['loggedintries'] == 0){
			$sqlupdate = "UPDATE users SET AccountLocked = 1 WHERE UserName = '$username'"; //locks user account
			queryInsert($conn, $sqlupdate);
			$_SESSION["Login.Error"] = "Your account has been locked. Please contact an admin to fix this.";
			header( 'Location: ../index.php');
			exit();
		}

		try{
			$rs = query($conn, $sql);
			$usertype;
			//successful login
			if($rs->num_rows==1){
				$_SESSION['loggedin']=1; 
				$rs->data_seek(0); 
				$row = $rs->fetch_assoc();
				unset($_SESSION['loggedintries']);
				$sqlupdate = "UPDATE users SET LoginAttempts = 5 WHERE UserName = '$username'"; //resets loginAttempts to 5 
				$login = "UPDATE users SET LoggedIn = 1 WHERE UserName = '$username'";  //updates database to say user is logged in
				queryInsert($conn, $sqlupdate);
				queryInsert($conn, $login);
	        	$_SESSION['usertype'] = $row['UserType']; //sets session variable for usertype
	        	$_SESSION['username'] = $row['UserName']; //sets session variable for username    	
			}
			//incorrect username or password	
			else{
				header( 'Location: ../index.php');
				$attemptsleft = --$_SESSION['loggedintries'];
				$sqlupdate = "UPDATE users SET LoginAttempts = $attemptsleft WHERE UserName = '$username'";
				queryInsert($conn, $sqlupdate);

				$_SESSION["Login.Error"] = 'Invalid credentials. You have ' . $_SESSION['loggedintries'] . ' log in attempts left';//redirect back to your login page
			}
			//used to check usertpye i.e. user or admin
			if($_SESSION['loggedin']===1){
				if($_SESSION['usertype']==='User'){
					header( 'Location: ../application.php');
				}
				else if($_SESSION['usertype']==='Admin'){
					header( 'Location: ../admin.php');
				}
			}
		}
		catch(Exception $e){
			echo "Message: ".$e->getMessage();
		}
	}
?>