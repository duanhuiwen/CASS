<?php
require_once("../common/auth_loginf.php"); //Login form generation function include
require_once("../common/auth_start.php");
//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php");

//Logout functionality, if logout flag set, perform logout.
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
			if($a->getAuthData('research_owner')==1){
				//Include the form needed for research creation.
				require_once("../functionality/utilities.php");
				require_once("../functionality/newResearch.php");
				//Resetting the timeout timer
				$a->setExpire($timeout, false);
			}else{
				echo "Access denied,you are not allowed to browse this data.";
			}
		}
	}
}

//page footer
require_once("../UI/layout/bottom.php");
?>