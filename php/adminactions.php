<?php
    include("../config/connection.php");  
    include("helperFunctionsTables.php");  
    include("helperFunctionsDatabase.php");
    session_start();
    $username = $_SESSION['username']; //stores the username once the user has logged on

    //gets username
    if($_POST['change']==="getusername"){
      echo $username;
    }
    else{

      $PK=$_POST["pk"]; //gets the name of the primary key column from the $_POST
      $table =$_POST["table"]; //gets the name of the table being edited from the $_POST
  
      //handles deletes
      if($_POST['change']==="delete"){
        
        //prevents admin from deleteing themselves
        if($_POST['primarykey'] == $username){
         exit();
        }
        $selectedID = $_POST['primarykey'];
        $sqldel = "CALL delete_user('$selectedID')";
        deleteRecord($conn,$sqldel);
      }

      //deals with delete of a request
      if($_POST['change']==="deleterequest"){
        $selectedID = $_POST['primarykey'];
        $sqldel = "CALL delete_request($selectedID)";
        deleteRecord($conn,$sqldel);
      }

      
      //loads in the user modal for when the admin is editing a users details
      if($_POST['change']==="loadusermodal"){
       $email;
       $fname;
       $lname;
       $usertype;
       $selectedID = $_POST['primarykey'];
       $sqlselect = "SELECT * FROM $table WHERE $PK = '$selectedID'";
        $rs = query($conn, $sqlselect);
        while($row = $rs->fetch_assoc()) {
                $email = $row["Email"];
                $fname = $row["FirstName"];
                $lname = $row["LastName"];
                $usertype = $row["UserType"];
                $accountlocked = $row["AccountLocked"];
                $loginattempts = $row["LoginAttempts"];
        }
        
        include "../FORMS/usermodal.html";
      }


      //saves the edited values that were submitted from user update modal
      if($_POST['change']==="edit"){
        $email = $_POST['email'];
        $fname = $_POST['firstname'];
        $lname = $_POST['lastname'];
        $userid = $_POST['primarykey'];
        $accountlocked = $_POST['accountlocked'];
        $loginattempts = $_POST['loginattempts'];

        $sqlupdate = "UPDATE $table SET FirstName='$fname', LastName = '$lname', Email = '$email', AccountLocked = $accountlocked, LoginAttempts = $loginattempts WHERE $PK= '$userid'";
        query($conn, $sqlupdate);
      }

      //adds a comment to a request
      if($_POST['change'] === 'editrequest'){
        $comment = $_POST['comment'];
        $requestid = $_POST['primarykey'];
        $sqlupdate = "UPDATE $table SET Comment = '$comment' WHERE $PK=$requestid";
        query($conn, $sqlupdate);
      }

      //simply generates the table for the admin page. Works for both the requests and users table
      if($_POST['change']==='none'){
        $sqlData="SELECT * FROM $table";  
        $sqlTitles="SHOW COLUMNS FROM $table";  
        $rsData=getTableData($conn,$sqlData);
        $rsTitles=getTableData($conn,$sqlTitles);
        $arrayData=getResultSet($rsData);
        $arrayTitles=getResultSet($rsTitles);
        generateTable($table, $PK, $arrayTitles, $arrayData);
      }
}
?>