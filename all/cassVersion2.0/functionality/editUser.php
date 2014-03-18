<?php
//Declaring the auth login form creation function and Starting authentication
require_once ("../common/auth_loginf.php");
require_once ("../common/auth_start.php");
//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php"); 

if($a->getAuthData('su_admin')==1){ //Check user level
		//Resetting the timeout timer
		$a->setExpire($timeout, false);
		require_once("../common/includes.php");
	if(isset($id)){
		echo "<div id=\"descriptiondiv\">";
		echo "<div class=\"descrheader\"><div class=\"headertext\"><h1>Username: $uname</h1><hr /></div></div>";
		echo "<span class=\"backlink\"><a href=\"../content/admin.php\"><- Back</a></span>";
		echo "<div class=\"description\">";
		echo("user ID:$id<br />");
		echo "<p><dl><dt>Change users authentication data</dt>";
		echo "<dd><a href=\"profile.php?id=".encrypt($id)."\"> >> Set new password</a></dd>";
		//echo "<dd><a href=\"\">Set new username</a></dd>";
		echo "</dl></p>";
	
		echo "<dl>";
		if($u->isSuperadmin() && $u->isResearchowner()){
			echo("<dt>User is a super admin and a research owner</dt>");
		}elseif($u->isResearchowner() && $u->isSuperadmin()==false){
			echo("<dt>User is a <b>Research owner</b></dt>");
		}elseif($u->isResearchowner()==false && $u->isSuperadmin()){
			echo("<dt>User is a <b>Super admin</b></dt>");
		}else{
			echo("<dt>User has <b>no administrator rights</b></dt>");
		}
		echo "<dd><a href=\"./addAllRights.php?id=".$id."\"> >> Change users Admin Tool rights</a></dd></dl>";
		require_once("../functionality/listUsers.php");
		echo "</div></div>";
		echo "<div id=\"descriptiondiv\">";
		echo "<div class=\"descrheader\"><div class=\"headertext\"><h1>Users roles in researches</h1><hr /></div></div>";
		echo "<div class=\"description\">";
		genUserForSuper("admin", $roles,$id);
		
		genUserForSuper("researcher", $roles,$id);
		
		genUserForSuper("subject", $roles,$id);
		echo "</div></div>";
	}else{
		echo "Error!";
	}	
}
//Bottom part of the layout
require_once("../UI/layout/bottom.php");
?>