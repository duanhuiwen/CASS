<?php
function genQueryList($result,$admin,$rLocked,$id=0){
	require_once("../functionality/utilities.php");
	echo "<div id=\"queriesdiv\">";
	echo"<div class=\"descrheader\"><div class=\"headertext\"><h1>Queries in the research</h1><hr /></div></div>";
	if($result!=false){
		echo "<div class=\"queriescontent\">";
			if(mysql_numrows($result)!=0){
				echo("<table cellspacing=\"10\"> <tr> <th>Name</th>");
					if($admin==true){ // It doesn't seem very important, Action could be shown anyhow
						echo " <th>Actions</th><td></td>";
					}
				while($row = mysql_fetch_assoc($result)){
					echo("<tr><td>");
					echo("<a href=\"./displayQuery.php?id=".encrypt($row['query_id'])."\">".$row['name']."</a>");
					echo("</td>");
					if($admin==true && $rLocked!="Ended" && $rLocked!="freezed"){
						genEditDelete($row['query_id'],$row['locked']);
					}
					echo("</tr>");
				}
			echo("</table>");
			}else{
				echo("There are no queries in this research.");
			}
	}else{
		echo("There are no queries in this research.");
	}
echo "</div></div>";
}

function genEditDelete($id,$locked){
		require_once("../functionality/utilities.php");
		if($locked==0){
			$enId = encrypt($id);
			echo("<td><a href=\"./manipQuery.php?id=$enId\">Edit questions</a></td> ");
			echo("<td><a href=\"#\" onClick=\"wantToRemoveQuery('$enId')\">Remove query</a></td>");
		}
}

function getQueryList($result2,$admin2,$rLocked2,$id2=0){
	require_once("../functionality/utilities.php");
	if($result2!=false){	// if the $result2 has value
		if(mysql_numrows($result2)!=0){
			$c = 0; // setting up a counter that will help to populate an array with the query data
			while($row2 = mysql_fetch_assoc($result2)){
				// insert the data into the $query_array
				$query_array[$c]['query_id'] = $row2['query_id'];
				$query_array[$c]['name'] = $row2['name'];
				if($admin2==true && $rLocked2!="Ended" && $rLocked2!="freezed"){ // does the same as the genEditDelete function
					if($row2['locked']==0){
						$query_array[$c]['edit'] = $row2['query_id'];
					}
				}
			$c++;	
			}
		}
	}
	return array ($query_array);
} // End of function getQueryList
?>