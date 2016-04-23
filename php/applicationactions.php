<?php
session_start();
include("../config/connection.php"); 
include("helperFunctionsDatabase.php");
$username = $_SESSION['username'];

//adding a notebook
if($_POST['action'] === 'addnotebook'){
    $userswrite = $_POST['sharedwrite'];
    $usersread = $_POST['sharedread'];
    $name =  $_POST['notebookname'];
    if($name == ''){
        exit();
    }
    $isshared;
    $numitems;
    $id;
    
    $allusers = array_filter(array_merge($userswrite, $usersread)); //merges the two arrays(userswrite, usersread)
    
    //checks to see if the notebook has the notebook been shared with other users
    if(empty($allusers)){
        $isshared = 0;
    }
    else{
        $isshared = 1;
        //runs through all usernames to ensure they are correct
        foreach($allusers as $user){
        //checks to make sure the user hasn't tried to share the notebook with themselves
        if($username === $user){
            echo "error";
            exit();
        }
        
        //checks to make sure the username(s) is valid
        $sqlquery = "SELECT * FROM users WHERE UserName = '$user'";
        $rs = query($conn, $sqlquery);
        if($rs->num_rows != 1){
            echo "error";
            exit();
        }
    }
    }
    
    //generates the pk 
    $pk = generaterandnumber("notebooks", "NotebookID", $conn);

    $sqlinsert = "CALL add_notebook('$pk', '$name', $isshared, '$username')";
    queryInsert($conn, $sqlinsert);
    
    //inserts users with write access into the database
    foreach($userswrite as $user){
        $sqladd = "CALL add_shared_notebook('$user', '$pk', 1)";
    	queryInsert($conn, $sqladd);
    } 
    //inserts users with read access into the database
    foreach($usersread as $user){
        $sqladd = "CALL add_shared_notebook('$user', '$pk', 0)";
        queryInsert($conn, $sqladd);
    }         
}

 //getting username for displaying on application page. 
 if($_POST['action']==="getusername"){
    echo $username;
  }

//saving a request to the database. 
if($_POST['action'] === 'request'){
    $message = $_POST['message'];
    $subject = $_POST['subject'];
    if($message == '' || $subject == ''){
        exit();
    }
    $date = date("Y/m/d");
    $comment = '';
    $sqlinsert = "CALL add_request('$username', '$subject', '$message', '$date', '$comment')";
    queryInsert($conn, $sqlinsert);

  }


//loading files from the database and echoing back to the code to the ajax call
if($_POST['action']==='loadfiles'){
    $notebookid = $_POST['notebookid'];
    $_SESSION['access'] = 'Write';
    $order = $_POST['order'];
    if($_SESSION['notebookstype'] === 'shared'){
        $sqlcheckaccess = "SELECT WriteAccess FROM users_has_notebooks WHERE NotebookID = '$notebookid' AND UserName = '$username'";
        $resultset = query($conn, $sqlcheckaccess);
        $resultset->data_seek(0); 
        $row = $resultset->fetch_assoc();
        $writeaccess = $row['WriteAccess'];
        
        //checks to see has user been granted write access to this notebook
        if($writeaccess == 0){
            echo "<h4>Read access only</h4>";
            $_SESSION['access'] = 'Read'; //stores their access to the notebook in a session variable
        }
    }
    
    $sqlselectfiles = "SELECT * FROM files WHERE NotebookID = '$notebookid' ORDER BY FileCounter $order";
    $rs = query($conn, $sqlselectfiles);
    echo "<ul id='loadcontent'>";
    if($rs->num_rows==0){
        echo "No files exist in this notebook";
    }
    else{
    while($row = $rs->fetch_assoc()){
        $fileid = $row['FileID'];
        $filename = $row['FileName'];
        echo "<li value=$fileid>$filename</li>";
    }
    }
    echo "</ul>";
  }

//adding a file to the database. 
if($_POST['action']==='addfile'){
     //if user has only read access to the notebook they are not permitted to add any files
     if($_SESSION['access'] === 'Read'){
        exit();
    }


    $filename = $_POST['filename'];
    $notebookid = $_POST['notebookid'];
    $text = 'place your code here';
    $output = 'place your output here';
    
    //prevents submission of an empty file name
    if($filename == ''){
        exit();
    }

    //generates the primary key for the file
    $pk = generaterandnumber("files", "FileID", $conn);
    $sqlinsert = "CALL add_file('$pk', '$filename', '$text', '$output', '$notebookid')";
    queryInsert($conn, $sqlinsert);
}

//loading content for a file and echoing it back to the ajax call. 
if($_POST['action']==='loadcontent'){
    if(isset($_SESSION['recordlocked'])){
        $id = $_SESSION['recordlocked'];
        unlockfile($conn, $username, $id);
    }
    $return = $_POST;
    $return['firstline'] = " ";
    
    $fileid = $_POST['fileid'];
    $return['lock'] = 'true';
    
    //if the user only has read access their is no need to place any locks on any files
    if($_SESSION['access'] != 'Read'){
        $sqlcheck = "SELECT `Lock` FROM files WHERE FileID = '$fileid'";
        $resultset = query($conn, $sqlcheck);
        $resultset -> data_seek(0);
        $row = $resultset->fetch_assoc();
        $user = $row['Lock'];
        if($user != ""){
            $return['firstline'] = "Write access granted to ". $user . "\n";
            $return['lock'] = 'true';
        }else{
            $return['lock'] = 'false';
            lockfile($conn, $username, $fileid);
            $_SESSION['recordlocked'] = $fileid;
        }    
    }

    //gets the content for the particular file
    $getcontent = "SELECT FileContent, Output FROM files WHERE FileID = '$fileid'";
    $rs = query($conn, $getcontent);
    while($row = $rs->fetch_assoc()){
        $return['content'] = $row['FileContent'];
        $return['output'] = $row['Output'];
    }
    $return["json"] = json_encode($return);
    echo json_encode($return); //returns json object 
}

//saving the content or code of a file to the database
if($_POST['action']==='savecontent'){
    //if their is a lock on a file or if the user only has read access, this prevents them from having
    //any content saved to the database
    if(($_SESSION['access'] === 'Read')|| ($_SESSION['lock'] === 'true')){
        exit();
    }
    
    $content = $conn->real_escape_string($_POST['content']);
    $notebookid = $_POST['notebookid'];
    $fileid = $_POST['fileid'];

    $sqlinsert = "UPDATE files SET FileContent = '$content' WHERE FileID = '$fileid'";  
    queryInsert($conn, $sqlinsert); //updates the file content field
  }

//saving the code output to the database. 
  if($_POST['action']==='saveoutput'){
    if(($_SESSION['access'] === 'Read')|| ($_SESSION['lock'] === 'true')){
        exit();
    }
    $content = $conn->real_escape_string($_POST['output']);
    $notebookid = $_POST['notebookid'];
    $fileid = $_POST['fileid'];

    $sqlinsert = "UPDATE files SET Output = '$content' WHERE FileID = '$fileid'"; 
    queryInsert($conn, $sqlinsert); //updates the file output field
  }

//deleting a notebook from the database. 
   if($_POST['action']==='deletenotebook'){
        $notebookid = $_POST['notebookid'];

        //ensures the user who created the notebook is the one who is attempting to delete it
        $sqlcheck = "SELECT NotebookID FROM notebooks WHERE NotebookID = '$notebookid' AND UserName = '$username'";
        $rs = query($conn, $sqlcheck);
        if($rs->num_rows != 1){
            echo 0;
            exit();
        }
        $sqldelete = "CALL delete_notebook('$notebookid')";
        deleteRecord($conn, $sqldelete);
        echo "success";
    }

//deleting a file from the database
  if($_POST['action']==='deletefile'){
    if(($_SESSION['access'] === 'Read')|| ($_SESSION['lock'] === 'true')){
        echo 0;
        exit();
    }
        $fileid = $_POST['fileid'];
        $sqldelete = "CALL delete_file('$fileid')";
        deleteRecord($conn, $sqldelete);
  }

//saving the name change of a file to the database. 
  if($_POST['action']==='updatefile'){
    echo "updatedfile";
    if(($_SESSION['access'] === 'Read') || ($_SESSION['lock'] === 'true')){
        echo "exit";
        exit();
    }
        $filename = $_POST['filename'];
        $fileid = $_POST['fileid'];
       
        $sqlupdate = "UPDATE files SET FileName = '$filename' WHERE FileID = '$fileid'";
        queryInsert($conn, $sqlupdate);
  }

  //function is used to get all the users the notebook has been shared with for when the user is editing it. this function returns a json object
  if($_POST['action']==='getaccess'){
    $notebookid = $_POST['notebookid'];
    $return = $_POST;
    $return['firstline'] = " ";
    $getusers = "SELECT UserName FROM users_has_notebooks WHERE NotebookID = '$notebookid' AND WriteAccess = 1";
    $rs = query($conn, $getusers);
    $writeaccess = "";

    //gets all users with write access
    while($row = $rs->fetch_assoc()){
        $writeaccess = $writeaccess . $row['UserName'] . "\n"; 
    }
    
    $return['writeaccess'] = $writeaccess;
    $notebookid = $_POST['notebookid'];
    $getaccess = "SELECT UserName FROM users_has_notebooks WHERE NotebookID = '$notebookid' AND WriteAccess = 0";
    $rs2 = query($conn, $getaccess);
    $readaccess = "";

    //gets all users with read access
     while($row = $rs2->fetch_assoc()){
        $readaccess = $readaccess .  $row['UserName'] . "\n";
    }
    $return['readaccess'] = $readaccess; 
    $return["json"] = json_encode($return);
    echo json_encode($return);
  }

  //this function is used to update the the name and users of a notebook. similar to the code for adding a new notebook
  if($_POST['action'] === 'updatenotebook'){
    $notebookid = $_POST['notebookid'];

    $sqlcheck = "SELECT NotebookID FROM notebooks WHERE NotebookID = '$notebookid' AND UserName = '$username'";
    $rs = query($conn, $sqlcheck);

    //checks to ensure the user trying to update the notebook is the one who created with
    if($rs->num_rows != 1){
        echo 0;
        exit();
    }

    $userswrite = $_POST['write'];
    $usersread = $_POST['read'];
    $name =  $_POST['notebookname'];
    $allusers = array_filter(array_merge($userswrite, $usersread));
    if($name == '' || $notebookid == ''){
        exit();
    }
    $isshared;
    $numitems;
    $id;
  
    //validates usernames
    if(empty($allusers)){
        $isshared = 0;
    }
    else{
        $isshared = 1;
        foreach($allusers as $user){
            if($username === $user){
                echo 0;
                exit();
            }
            $sqlquery = "SELECT * FROM users WHERE UserName = '$user'";
            $rs = query($conn, $sqlquery);
            if($rs->num_rows != 1){
                echo 0;
                exit();
            }
        }
    }
    
    //updates name for notebook
    $sqlupdate = "UPDATE notebooks SET notebookname = '$name', shared = $isshared WHERE NotebookID = '$notebookid'";
    query($conn, $sqlupdate);
    
    //removes all current read and write users
    $sqldelete = "DELETE FROM users_has_notebooks WHERE NoteBookID = '$notebookid'";
    query($conn, $sqldelete);

    //adds write users 
    foreach($userswrite as $user){
        $sqladd = "CALL add_shared_notebook('$user', '$notebookid', 1)";
        queryInsert($conn, $sqladd);
    } 

    //adds read users
    foreach($usersread as $user){
        $sqladd = "CALL add_shared_notebook('$user', '$notebookid', 0)";
        queryInsert($conn, $sqladd);
    }          
    echo "success";
  }
  
  //used to remove a lock from a file
    if($_POST['action'] === 'removelock'){
        $id = $_SESSION['recordlocked'];
        unlockfile($conn, $id);
    }

    //removes a notebook shared with the user
    if($_POST['action'] === 'removenotebook'){
        $id = $_POST['notebookid'];
        $sqlremove = "DELETE FROM users_has_notebooks WHERE NoteBookID = '$id' AND UserName = '$username'";
        query($conn, $sqlremove);
    }
?>