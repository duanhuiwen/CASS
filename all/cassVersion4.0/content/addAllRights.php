<?php
/*
 * This file is used on the Super admin site to change the administrator rights of the
 * user. 
 */
//Login form generation function include
require_once("../common/auth_loginf.php");
require_once("../common/auth_start.php");
require_once("../common/includes.php"); //Class includes
$id=decrypt($_GET['id']);
//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php");
//Logout functionality, if logout flag set, perform logout.
if ($_GET['action'] == "logout" && $a->checkAuth()) {
    $a->logout();
    $a->start();
}else{
	if ($a->checkAuth()) { ///Start secured content
		
		$UID=$a->getAuthData('uid'); //get user ID
		$u = new User($UID);	
		if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
			echo "Access denied! Subjects can't login in to the admin tool.";
		}else{
			if($a->getAuthData('su_admin')==1){	
				$u->unlock();
				//Resetting the timeout timer
				$a->setExpire($timeout, false);
				if(isset($_GET['id'])){
					$userid = $_GET['id'];
				}else{
					$userid=$_POST['id'];
				}
				$u= new User($userid);
				$username = $u->getName();
				$superad = $u->isSuperadmin();
				$researchO = $u->isResearchowner();
				echo "<div id=\"descriptiondiv\">";
				echo "<div class=\"descrheader\"><div class=\"headertext\"><h1>Add user rights</h1><hr /></div></div>";
				echo "<span class=\"backlink\"><a href=\"../content/editUser.php?id=$userid\"><- Back</a></span>";
				echo "<div class=\"description\">";
				echo("<form id=\"addallrightsform\" method=\"POST\" action=\"./addAllRights_act.php\">");
				echo("<table>");
				echo("<tr><th>Name</th><th>Super Admin</th><th>Research Owner</th></tr>");
				echo("<tr><td>");
				echo("<a href=\"./showuser.php?id=".encrypt($userid)."\">$username</a>");
				echo("<input type=\"hidden\" name=\"suadmin\" value=\"0\" />");
				echo("</td><td>Super Admin<input type=\"checkbox\" name=\"suadmin\" value=\"1\" class=\"checkInput\" ");
				if($superad){
					echo("checked");
				}
				echo("/></td>");
				echo("<input type=\"hidden\" name=\"researchO\" value=\"0\" />");
				echo("</td><td>Research Owner<input type=\"checkbox\" name=\"researchO\" value=\"1\" class=\"checkInput\" ");
				if($researchO){
					echo("checked");
				}
				echo("/></td>");
				echo "<input type=\"hidden\" name=\"id\" value=\"$userid\" />";
				echo("<tr><td><a class=\"button\" href=\"#\" onclick=\"this.blur();sendform('addallrightsform')\"><span>Add rights</span></a></td></tr>");
				echo("</table>");
				echo("</form>");
				echo "</div></div>";
			}else{
				echo "Access denied,you are not allowed to browse this data.";
			}
    	}
	}
}

//page footer
include("../UI/layout/bottom.php");
?>