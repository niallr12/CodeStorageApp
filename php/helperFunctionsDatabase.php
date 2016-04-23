<?php
	//function used for insert queries
	function queryInsert($connection,$sql){
		try {
			if ($connection->query($sql)===TRUE)  
			{
				return 1;  
			}
			else
			{
				return 0;  
			}
		}
		//catch exception
		catch(Exception $e) {
			header("Location:".__USER_ERROR_PAGE);		
		}
	}

	//function used for delete queries
	function deleteRecord($connection,$sql){
		try {
			if ($connection->query($sql)===TRUE)  
			{
				return 1;  
			}
			else
			{
				return 0;  
			}
		}
		//catch exception
		catch(Exception $e) {	
			header("Location:".__USER_ERROR_PAGE);
				
		}
	}


	//function used for queries returning resultsets
	function query($connection,$sql){
		try {
			$rs=$connection->query($sql);
			return $rs;
		}
		catch(Exception $e) {
			header("Location:".__USER_ERROR_PAGE);		
		}
	}

	//generates primary key for notebooks and files
	function generaterandnumber($tablename, $colname, $connection){
		$pk;
		do{
			$pk = guidv4(openssl_random_pseudo_bytes(16));
			$sqlcheck = "SELECT $colname FROM $tablename WHERE $colname = '$pk'";
			$rs = $connection->query($sqlcheck);
		}while($rs->num_rows!==0);
		return $pk;

	}

	//generates random string of characters
	function guidv4($data){
	    assert(strlen($data) == 16);

	    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
	    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

	    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

	//test values when user is signing up for ununsual characters and empty sapces
	function testvalues($data){
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	//function to lock file
	function lockfile($conn, $username, $fileid){
		$sqlupdate = "UPDATE files SET `Lock` = '$username' WHERE FileID = '$fileid'";
	    $conn->query($sqlupdate);
	}

	//function to unlock file
	function unlockfile($conn, $username, $fileid){
		$changelock = "UPDATE files SET `Lock` = '' WHERE FileID = '$fileid' AND `Lock` = '$username'";
	    $conn->query($changelock);
	}

?>