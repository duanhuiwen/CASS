<?php
//Login form generation function include


include "../settings/dbsettings.php";


		$query= "SELECT `research_id` FROM `tbl_subject` WHERE `UID` ='459';";
		$result = $mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			echo "SelectError";
		}else{
			$native_result = $result->getResource();
			echo mysql_numrows($native_result);
						$row = mysql_numrows($native_result);
			echo $row;
			//echo $result->fetchRow();
			//echo $result;					
		}
		
	
		
?>