<?php

	function getTableData($connection,$sql){
		try {
			$rs=$connection->query($sql);
			return $rs;
		}
		//catch exception
		catch(Exception $e) {
			header("Location:".__USER_ERROR_PAGE);		
		}
	}

	function getResultSet($rs){
		$arr = $rs->fetch_all(MYSQLI_ASSOC);  //put the result into an array
		return $arr;
	}



	function generateTable($tableName, $primaryKey, $titlesResultSet, $dataResultSet){
		//use resultsets to generate HTML tables

		echo "<table class='table'>";

		//first - create the table caption and headings
		echo "<caption>".strtoupper($tableName)." TABLE</caption>";
		echo '<tr>';
		foreach($titlesResultSet as $fieldName) {
			echo '<th>'.$fieldName['Field'].'</th>';
		}
		echo '<th>DELETE</th>';
		echo '<th>EDIT</th>';
		echo '</tr>';

		//then show the data
		foreach($dataResultSet as $row) {
			echo '<tr>';
			foreach($titlesResultSet as $fieldName) {
				echo '<td>'.$row[$fieldName['Field']].'</td>';}
			echo '<td>';
			//set the button values and display the button ton the form:
			$id=$row[$primaryKey];  //get the current PK value
			$buttonText="Delete";
			include '../FORMS/delbutton.txt';
			echo '</td>';
			echo '<td>';
			//set the button values and display the button ton the form:
			$id=$row[$primaryKey];  //get the current PK value
			$buttonText="Edit";
			include '../FORMS/editbutton.txt';
			echo '</td>';
			echo '</tr>';
			}
		echo "</table>";
	}
?>