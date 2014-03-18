<?php
function UserResearchList($id){
	require_once("../functionality/utilities.php");
	$sql=new ResearchSQLQueryer();	// creates a new SQL object for researches
	$researchList=$sql->listResearchByUser($id);	// this returns an SQL object with the researches in what the user participates
	$rows=mysql_num_rows($researchList);	// number of the rows in the SQl object
	if($rows<1){
		echo("You are not participating in any research.<br />");
	}else{
		echo("<table cellspacing=\"10\" width=\"450px\">"); //Start table
		echo("<tr><th>Research</th><th>Description</th><th>Status</th><th>Your Role</th>");
		echo "</tr>";
		for($i=0; $i<$rows; $i++){	//gets the data from the research object
			$name=mysql_result($researchList, $i, 'research_name');	// name of the research
			$rid=mysql_result($researchList, $i, 'research_id');	// research ID
			$rdescr=mysql_result($researchList, $i, 'research_descr');	// description of the research
			$rstart=mysql_result($researchList, $i, 'startTime');	// start date
			$rend=mysql_result($researchList, $i, 'endTime');	// end date
			$subject=mysql_result($researchList, $i, 'subject');	// if the user is a subject the value is 1, otherwise 0
			$researcher=mysql_result($researchList, $i, 'researcher');	// if the user is a researcher the value is 1, otherwise 0
			$admin=mysql_result($researchList, $i, 'admin');	// if the user is a research administrator the value is 1, otherwise 0
			$status = $sql->getStatus($rid);	// returns a value depending on the start and end dates
			$role = "";	// resetting the $role variable
			if($subject==1){	// $role variable will store a string value showing the role of the user in the research
				$role .= "respondent<br />";
			}
			if($researcher==1){
				$role .= "researcher<br />";
			}
			if($admin==1){
				$role .= "admin";
			}
			echo("<tr>"); 
			//Display link
			echo("<td>");
			if($admin==1 || $researcher==1){
				echo("<a href=\"./displayResearch.php?id=".encrypt($rid)."\">$name</a>");
			}else{
				echo $name;
			}
			echo("</td>"); 
			//display description
			echo("<td>"); 
			echo("$rdescr");
			echo("</td>");
			//Displaying status field
			echo("<td>");
			if($status=="Ended"){
				echo $status;
			}elseif($status=="On Progress"){
				echo $status."<br />Ends: <br />$rend";
			}elseif($status=="Starts"){
				echo $status.": $rstart";
			}else{
				echo "Status not available";
			}
			echo("</td>"); 
			//Displaying role field
			echo("<td>"); 
			echo("$role");
			echo("</td>"); 
			echo("</tr>");
			
			
		}//endfor
		echo("</table>");
	}//end else
	
	
}//end function

	// this may be not in use, Super Admin cannot see any research that he/she does not participate in
	function superAdminResearchList(){
		$researchList=SQLQueryer::ListAllResearch();
		//var_dump($researchList);
		$rows=mysql_num_rows($researchList);
		if($rows<1){
			echo("There are no research projects in the database<br />");
		}else{
		
			echo("<table>"); //Start table
			echo("<tr><th>Research</th><th>Description</th><th>Actions</th></tr>");
			
			for($i=0; $i<$rows; $i++){
				$name=mysql_result($researchList, $i, 'research_name');
				$rid=mysql_result($researchList, $i, 'research_id');
				$rdescr=mysql_result($researchList, $i, 'research_descr');
	
				echo("<tr>"); //Start a new row
				//Display link
				echo("<td>"); //Start a new cell
				echo("<a href=\"./displayResearch.php?id=$rid\">$name</a>");
				echo("</td>"); //End cell
				//display description
				echo("<td>"); //Start a new cell
				echo("$rdescr");
				echo("</td>"); //End cell
				//Displaying status field
				echo("<td>"); //Start a new cell
				echo("<a href=\"./editResearch.php?id=$rid\">edit</a><br />");
				echo("<a href=\"./admin.php?id=$rid&action=rm\">remove</a><br />");
				echo("<a href=\"./addRights.php?id=$rid\">rights</a>");
				echo("</td>"); //End cell
				echo("</tr>");//End the row
			}//end for
			echo "<tr></tr>";
			echo("</table>");
		}//end else
	}//end function
?>
