<?php
	session_start();
  include("../config/connection.php"); 
  include("helperFunctionsDatabase.php");
  
  $select = $_POST['select']; //indicates type of notebooks to be loaded, 1 = sharednotebooks, 0 = creatednotebooks
  $username = $_SESSION['username'];
  $order = $_POST['order'];
  $rs;


   if(isset($_SESSION['recordlocked'])){
        $id = $_SESSION['recordlocked'];
        unlockfile($conn, $username, $id);
    }
  //creates query to get shared notebooks
  if($select==1){
    $getnotebooks = "SELECT NotebookID FROM users_has_notebooks WHERE username = '$username' ORDER BY SharedNotebookCounter $order";
    $rs = query($conn, $getnotebooks);
    $_SESSION['notebookstype'] = 'shared';
  }

  //creates query to get created notebooks
  else if($select==0){
    $sqlquery = "SELECT NotebookID FROM notebooks WHERE UserName = '$username' ORDER BY NotebookCounter $order";
    $rs = query($conn, $sqlquery);
    $_SESSION['notebookstype'] = 'created';
  }

  //echos back all of the notebooks for the particular choice the user choice
  if($rs->num_rows>0){
    echo "<ul id='loadfiles'>";
  	while($row = $rs->fetch_assoc()){
  		$notebookid = $row['NotebookID'];
      //query is responsible for getting the names of the notebooks
  		$sqlstatement = "SELECT * FROM notebooks WHERE notebookid = '$notebookid' ORDER BY NotebookCounter $order";
  		$rs2 = query($conn, $sqlstatement);
  		$rs2->data_seek(0);
  		$row2 = $rs2->fetch_assoc();
      $nbname = $row2['NotebookName'];
      echo "<li value='$notebookid'>$nbname</li>";
  	}
    echo "</ul>";
  }
  else{
  	echo "No notebooks exist";
  }
?>