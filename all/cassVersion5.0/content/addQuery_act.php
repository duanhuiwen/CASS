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
			$id = decrypt($_POST['researchid']);
			//Check user level
			$r = new Research($id);
			if($r->users->isLocalAdmin($UID)){
				//Include the form needed for Query creation.		
				require_once("../functionality/newQuery.php");
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