<?php
//Login form generation function include
require_once("../common/auth_loginf.php");
require_once("../common/auth_start.php");

require_once("../UI/layout/top.php");

//This is run, if user wishes to logout
if($_GET['action'] == "logout" && $a->checkAuth()){ 
    $a->logout();
    $a->start();
}else{
	if($a->checkAuth()){ ///Start secured content
		require_once("../common/includes.php"); //Class includes
		$UID=$a->getAuthData('uid'); //get user ID
		$u = new User($UID);
	
		if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
			echo "Access denied! Subjects can't login in to the admin tool.";
		}else{ 
			$u->unlock();
			//echo("<h1> Change password</h1>"); //The headline
			require_once("../functionality/changepwd_act.php"); 
			$admin=$a->getAuthData('su_admin');
			$rowner=$a->getAuthData('research_owner');
			echo("</table>");
		}
		}else{
			echo("<h2> You are not allowed to add new users.</h2>");
		}
	}

//page footer
echo("<br /><br /><br /><br /><br /><br /><br /><br />");
include("../UI/layout/bottom.php"); //the bottom part of the layout

?>