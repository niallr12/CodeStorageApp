<?php
	session_start();  
	include("../config/connection.php"); 
	include("helperFunctionsDatabase.php");
	$username = $_SESSION['username'];

	//if the user has a lock on a file it is removed
	if(isset($_SESSION['recordlocked'])){
        $id = $_SESSION['recordlocked'];
        $changelock = "UPDATE files SET `Lock` = '' WHERE FileID = '$id'";
        queryInsert($conn, $changelock);
    }

    //sets LoggedIn value to 0 
    $logout = "UPDATE users SET LoggedIn = 0 WHERE UserName = '$username'";
    queryInsert($conn, $logout);

    //destroys all the session values and session cookie
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
	   $params = session_get_cookie_params();
	   setcookie(session_name(), '', time() - 42000,
	       $params["path"], $params["domain"],
	       $params["secure"], $params["httponly"]);
	}
	session_destroy();
	
	//returns user to homepage
	header( 'Location: ../index.php');


?>