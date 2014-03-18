<?php
function genUserList($heading, $result,$admin=0,$research_id=0){ //TODO: Change the result not to have a def value when not needed
	require_once("../functionality/utilities.php");
	echo("<h2>$heading</h2>");
	if(mysql_num_rows($result)!=0){
		echo("<table cellpadding=\"0\" cellspacing=\"0\"><tr><th>Name</th></tr>");
		/*if($heading == 'Subjects'){
			echo "<td><a href=\"./addnsubjects.php?id=$research_id\">Add Subjects to research</a></td>";
		}*/
		
		while($row = mysql_fetch_assoc($result)){ 
			echo("<tr><td>");
			echo("<a href=\"./showuser.php?id=".encrypt($row['UID'])."\">".$row['username']."</a>");
			echo("</td></tr>");
		}
		echo("</table>");
	}else{
		echo("There are no users of this type in this research.");
		if($heading=='Respondents' && $admin==true){
			echo "<br />";
			echo "<a href=\"./addnsubjects.php?id=".encrypt($research_id)."\">Add new respondents</a>";
		}	
	}
}

function genUser($heading, $result,$id,$uid=0){
	require_once("../functionality/utilities.php");
	//If need start a table
	for($j=0;$j<count($result);$j++){
		if($result[$j][$heading] == 1){
			$j=count($result)+1;
			$table = true;
			if($heading == 'subject'){
				echo("<h2>respondent</h2>");
			}else{
				echo("<h2>$heading</h2>"); 
			}
			echo("<table cellspacing=\"10\"> <tr> <td>Research name</td> <td>Research ID</td><td></td></tr>");
		}
	}
			
	for($i=0;$i<count($result);$i++){
		if($result[$i][$heading]==1){				
			echo("<tr><td>");
			echo("<a href=\"./displayResearch.php?id=".encrypt($result[$i]['research'])."\">".$result[$i]['researchname']);
			echo("</td><td>".$result[$i]['research']."</td>");
			if($heading == 'subject'){
				$r = new Research($result[$i]['research']);
				if($r->users->isLocalAdmin($uid) && $r->isLocked()!="Ended" && $r->isLocked()!="freezed"){
					$privar = $r->users->localPrivar($id);
					echo "<td>";
					if(!empty($privar)){
						echo("<a href=\"./editPrivar.php?id=".encrypt($id)."&rid=".encrypt($result[$i]['research'])."\"> Edit users private varibles in this research</a>");
					}else{
						echo("<a href=\"./addPrivar.php?id=".encrypt($id)."&rid=".encrypt($result[$i]['research'])."\"> Add private varibles for user in this research</a>");
					}
					echo "</td>";
					echo "<td>";
					$u = new User($id);
					$btId = $u->subjects->getBt_id($result[$i]['research']);
					//echo "Bluetooth ID: $btId<br /><br />";
					echo "Token: $btId<br /><br />";
					//echo "<a href=\"addBtId.php?id=".encrypt($id)."&rid=".encrypt($result[$i]['research'])."\">Set Bluetooth ID for this respondent in this research</a></td>";
					//echo "<a href=\"addBtId.php?id=".encrypt($id)."&rid=".encrypt($result[$i]['research'])."\">Set token for this respondent in this research</a></td>";
				}
			}
			echo "</tr>";			
		}
	}
	//if table started, close table
	if($table == true){
		echo("</table>");
	}	
}//end of function

function genUserForSuper($heading, $result,$id){
	//If need start a table
	for($j=0;$j<count($result);$j++){
		if($result[$j][$heading] == 1){
			$j=count($result)+1;
			$table = true;
			echo "<div id=\"descriptiondiv\">";
			echo "<div class=\"descrheader\"><div class=\"headertext\"><h2>$heading</h2></div></div>";
			echo "<div class=\"description\">";
			echo("<table cellspacing=\"10\"> <tr> <th>Research name</th> <th>Research ID</th></tr>");
		}
	}
			
	for($i=0;$i<count($result);$i++){
		if($result[$i][$heading]==1){				
			echo("<tr><td>");
			echo $result[$i]['researchname'];
			echo("</td><td>".$result[$i]['research']."</td>");
			echo "</tr>";			
		}
	}
	//if table started, close table
	if($table == true){
		echo("</table></div></div>");
	}	
}//end of function

function SuperAdminsListAllUsers(){
	$userList=SQLQueryer::ListAllUsers();
	//var_dump($researchList);
	$rows=mysql_num_rows($userList);
	if($rows<1){
		echo("Error!<br />");
	}else{
	
	echo("<table cellspacing=\"20\">"); //Start table
	echo("<tr><th>User</th><th>Roles</th><th>Actions</th></tr>");
	for($i=0; $i<$rows; $i++){
		$name=mysql_result($userList, $i, 'username');
		$userid=mysql_result($userList, $i, 'UID');
		$su_admin=mysql_result($userList, $i, 'su_admin');
		$research_owner=mysql_result($userList, $i, 'research_owner');

		echo("<tr>"); //Start a new row
		//Display link
		echo("<td>"); //Start a new cell
		echo("<a href=\"./showuser.php?id=".encrypt($userid)."\">$name</a>");
		echo("</td>"); //End cell
		//display roles
		echo("<td>"); //Start a new cell
		if($su_admin == 1){
			echo "Super Administrator<br />";
		}elseif($research_owner == 1){
			echo "Research Owner<br />";
		}else{
			$u = new User($userid);
			$roles = $u->getRoles();
			$rol = 0;
			for($j=0;$j<count($roles);$j++){
				if($roles[$j]['admin']==1){
					//echo "Admin<br />";
					if($rol<3){
						$rol = 3;
					}
				}
				if($roles[$j]['researcher']==1){
					//echo "Researcher<br />";
					if($rol<2){
						$rol = 2;
					}
				}
				if($roles[$j]['subject']==1){
					//echo "Subject<br />";
					if($rol<1){
						$rol = 1;
					}
				}
			}
			if($rol==3){
				echo "Admin<br />";
			}elseif($rol==2){
				echo "Researcher<br />";
			}elseif($rol==1){
				echo "Subject<br />";
			}else{
				echo "User has no roles<br />";
			}
		}
		echo("</td>"); //End cell
		//Displaying actions
		echo("<td>"); //Start a new cell
		echo("<a href=\"./editUser.php?id=$userid\">edit</a></td><td>");
		echo("<a href=\"#\" onClick=wantToRemoveUser($userid)>remove</a></td><td>");
		echo("<a href=\"./addAllRights.php?id=$userid\">rights</a>");
		echo("</td>"); //End cell
		echo("</tr>");//End the row
	}//end for
	echo "<tr></tr>";
	//echo($i);
	echo("</table>");
	}//end else
}//end function

function genUserListForDoc($heading, $result,$id=0){ 
	$doc ="<h3>$heading</h3>";
	if(mysql_num_rows($result)!=0){
		$doc .= "<table> <tr> <th>Name</th>";
		if($heading=="Subjects"){
			$doc .="<th>Token</th><th>Private variables</th>";
		}
		$doc .="</tr>";
			while($row = mysql_fetch_assoc($result)){
				$doc .="<tr><td>".$row['username']."</td>";
				if($heading=="Subjects"){
					$s = new Subject($row['UID']);
					$bt = $s->getBt_id($id);
					$privar = $s->getPrivar();
					$doc .="<td>$bt</td>";
					if(count($privar)>0){
						$doc .="<td>";
						for($i=0;$i<count($privar);$i++){
							$doc .="[".$privar[$i]['number']."]=".$privar[$i]['privar']." ";
						}
						$doc .="</td>";
					}
				}
				$doc .="</tr>";
			}
		$doc .="</table>";
	}else{
		$doc .="There are no users of this type in this research.";	
	}
	return $doc;
}


?>
