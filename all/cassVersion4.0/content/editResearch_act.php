<?php
//Login form generation function include
require_once("../common/auth_loginf.php");
require_once("../common/auth_start.php");
//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php"); 

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
			$rid = decrypt($_POST['rid']);
			$res = new Research($rid); // creating research object
			if($res->users->isLocalAdmin($UID)){ // checking if the user is local administrator
				//Include the form needed for Query creation.
				require_once("../functionality/updateResearch.php");
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