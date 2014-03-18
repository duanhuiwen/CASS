<?php
//Login form generation function include
require_once("../common/auth_loginf.php");
require_once("../common/auth_start.php");
//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php");

if ($_GET['action'] == "logout" && $a->checkAuth()) {
    $a->logout();
    $a->start();
}else{
	if ($a->checkAuth()) { ///Start secured content
		require_once("../common/includes.php"); //Class includes
		$UID=$a->getAuthData('uid'); //get user ID
		$u = new User($UID);
		
		if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
			echo "Access denied! Subjects can't login in to the admin tool.";
		}else{
			$u->unlock();
			//Resetting the timeout timer
			$a->setExpire($timeout, false);
			echo "<div id=\"navpath\"><a href=\"../content/index.php\"> Home </a> >> <a href=\"#\"> Add new user </a></div>";
			require_once("../functionality/adduser_frm.php");
		}
	}//end secured content
}//endif

//page footer
include("../UI/layout/bottom.php");

?>