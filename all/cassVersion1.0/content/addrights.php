<?php
//Login form generation function include
require_once("../common/auth_loginf.php");
require_once("../common/auth_start.php");
require_once("../common/includes.php"); //Class includes

if($id==null){	
	$id=decrypt($_GET['id']);
}
$research_id = $_GET['id'];
$research_menu = true;
//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php");

//Logout functionality, if logout flag set, perform logout.
if($_GET['action'] == "logout" && $a->checkAuth()){ 
    $a->logout();
    $a->start();
}else{
	if ($a->checkAuth()) { ///Start secured content

		$UID=$a->getAuthData('uid'); //get user ID
		$u = new User($UID);
		
		if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
			echo "Access denied! Subjects can't login in to the admin tool.";
		}else{
			$UID=$a->getAuthData('uid'); //get user ID
			//Resetting the timeout timer
			$a->setExpire($timeout, false);			
			if(isset($_GET['id'])){
				$rid = $id;
			}else{
				$rid=decrypt($_POST['id']);
			}
			
			$r = new Research($rid);
			$locked = $r->isLocked();
			if($r->users->isLocalAdmin($UID)){
				if($locked!="Ended" && $locked!="freezed"){
				$u->unlock();
				$sqlq= new UserSQLQueryer(); // creates a new UserSQLQueryer object
				$result= $sqlq->getAllUsers();
				echo "<div id=\"navpath\"><a href=\"index.php\"> Home </a> >> <a href=\"displayResearch.php?id=$research_id\"> ".$r->getName()." </a> >> <a href=\"#\"> manage roles </a></div>";
				echo "<div id=\"addrightsdiv\">";	
				echo "<div class=\"descrheader\"><div class=\"headertext\"><div id=\"help\" onclick=\"openhelp('manrights');\">?</div><h1>Manage user roles in this research</h1><hr /></div></div>";
				echo "<div class=\"addrightscontent\"><br />";
				echo '<a name="top"></a>Search by the first letter of the username: <a href="#0_9">0..9</a> <a href="#A_E">A..E</a> <a href="#F_J">F..J</a> <a href="#K_O">K..O</a> <a href="#P_T">P..T</a> <a href="#U_Z">U..Z</a> <a href="#xxx">Other</a>'; // links to the different groups of the users
				echo("<form id=\"addrightsform\" method=\"POST\" action=\"./addrights_act.php\">");
				echo("<table cellspacing=\"10\">");
				echo("<tr><th>Name</th><th>Respondent</th><th>Researcher</th><th>Administrator</th></tr>");
				while($row = mysql_fetch_assoc($result)){
					$localadmin = $sqlq->isLocalAdmin($rid,$row['UID']);
					$localres = $sqlq->isLocalResearcher($rid,$row['UID']);
					$localsub = $sqlq->isLocalSubject($rid,$row['UID']);
					$rights[$row['UID']] = array(
						'admin' => $localadmin,
						'researcher' => $localres,
						'subject' => $localsub
					);
					// Making a sort for the usernames, setting up anchors for the different name groups
					$firstLetter = $row['username']{0};
					if ((stristr('0123456789',$firstLetter) != false) && ($flag_09 == 0)){
						echo '<tr><td><a name="0_9"></a></td><td><a href="#top">To the top</a></td><td><a href="#end">To finish</a></td></tr>';
						$flag_09 = 1;
					}elseif((stristr('abcde',$firstLetter) != false) && ($flag_ae == 0)){
						echo '<tr><td><a name="A_E"></a></td><td><a href="#top">To the top</a></td><td><a href="#end">To finish</a></td></tr>';
						$flag_ae = 1;
					}elseif((stristr('fghij',$firstLetter) != false) && ($flag_fj == 0)){
						echo '<tr><td><a name="F_J"></a></td><td><a href="#top">To the top</a></td><td><a href="#end">To finish</a></td></tr>';
						$flag_fj = 1;
					}elseif((stristr('klmno',$firstLetter) != false) && ($flag_ko == 0)){
						echo '<tr><td><a name="K_O"></a></td><td><a href="#top">To the top</a></td><td><a href="#end">To finish</a></td></tr>';
						$flag_ko = 1;
					}elseif((stristr('pqrst',$firstLetter) != false) && ($flag_pt == 0)){
						echo '<tr><td><a name="P_T"></a></td><td><a href="#top">To the top</a></td><td><a href="#end">To finish</a></td></tr>';
						$flag_pt = 1;
					}elseif((stristr('uvwxyzåäö',$firstLetter) != false) && ($flag_uz == 0)){
						echo '<tr><td><a name="U_Z"></a></td><td><a href="#top">To the top</a></td><td><a href="#end">To finish</a></td></tr>';
						$flag_uz = 1;
					}elseif(($flag_uz == 1) && (stristr('uvwxyzåäö',$firstLetter) == false) && ($flag_xxx == 0)){
						echo '<tr><td><a name="xxx"></a></td><td><a href="#top">To the top</a></td><td><a href="#end">To finish</a></td></tr>';
						$flag_xxx = 1;
					}
					echo("<tr><td>");
					$s = $row['UID'].";subject";
					echo("<a href=\"./showuser.php?id=".encrypt($row['UID'])."\">".$row['username']."</a>");
					echo("<input type=\"hidden\" name=\"$s\" value=\"0\" />");
					echo "</td>";
					$sub = new Subject($row['UID']);
					$part = $sub->participatingIn(); // returns the ids of the research
if(!is_string($part)){ 
					if(mysql_numrows($part)>0){ // if the user participates as a subject in any research
						for($i=0;$i<mysql_numrows($part);$i++){
							$partRid = mysql_result($part,$i,'research_id');
							if($r->checkOverlap($partRid)){
								$overlaps = true;
								break;
							}else{
								$overlaps = false;
							}
						}
					}else{
						$overlaps=false;
					}
}
					echo("<td>");
					//if(!$sub->isActive()){
						if($overlaps!=1 || $localsub){
							echo "Respondent <input type=\"checkbox\" name=\"$s\" value=\"1\" class=\"checkInput\" ";
							if($localsub){
								echo("checked />");
							}
						}
					//}
					//echo "over: $overlaps, local: $localsub";
					echo("</td>");
					//$rc = encrypt($row['UID'].";researcher");
					$rc = $row['UID'].";researcher";
					echo("<input type=\"hidden\" name=\"$rc\" value=\"0\" />");
					echo("</td><td>Researcher <input type=\"checkbox\" name=\"$rc\" value=\"1\" class=\"checkInput\" ");
					if($localres){
						echo("checked");
					}
					echo("/></td>");
					//$a = encrypt($row['UID'].";admin");
					$a = $row['UID'].";admin";
					echo("<input type=\"hidden\" name=\"$a\" value=\"0\" />");
					echo("</td><td>Admin <input type=\"checkbox\" name=\"$a\" value=\"1\" class=\"checkInput\" ");
					if($localadmin){
						echo("checked");
					}
					echo("/></td>");
					echo "<td><input type=\"hidden\" name=\"rights[".encrypt($row['UID'])."][admin]\" value=\"$localadmin\" />";
					echo "<input type=\"hidden\" name=\"rights[".encrypt($row['UID'])."][res]\" value=\"$localres\" />";
					echo "<input type=\"hidden\" name=\"rights[".encrypt($row['UID'])."][sub]\" value=\"$localsub\" /></td>";
				}
				echo "<input type=\"hidden\" name=\"id\" value=\"".encrypt($rid)."\" />";
				//echo("<tr><td><br /><input type=\"submit\" value=\"Add rights\" /></td></tr>");
				echo("<tr><td><a class=\"button\" href=\"#\" onclick=\"this.blur();sendform('addrightsform')\"><span>Add rights</span></a></td>");
				echo '<td><a class="button" href="displayResearch.php?id='.$research_id.'"><span>Back</span></a></td><td><a name="end"></a></td></tr>';
				echo("</table>");
				echo("</form>");
				echo "</div>";
				echo "</div>";
				}else{
					echo "<h1>Research has ended! Cannot edit it anymore</h1>";
					echo '<p><a class="button" href="displayResearch.php?id='.$research_id.'"><span>Back</span></a></td><td><a name="end"></a></p>';
				}
				}else{
					echo "Access denied,you are not allowed to browse this data.";
				}
		}
	}//end secured content
}//endif

//page footer
include("../UI/layout/bottom.php");
?>