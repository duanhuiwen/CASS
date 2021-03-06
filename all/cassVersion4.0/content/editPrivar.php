<?php
require_once ("../common/auth_loginf.php"); //Declaring the auth login form creation function
require_once ("../common/auth_start.php"); //Starting authentication
require_once("../common/includes.php"); //Class includes

if(isset($_GET['rid'])){
	$research_id = $_GET['rid'];
	$rid = decrypt($_GET['rid']);
}else{
	$research_id = $_POST['rid'];
	$rid = decrypt($_POST['rid']);
}
$research_menu = true;
//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php");

//Logout functionality, if logout flag set, perform logout.
if($_GET['action'] == "logout" && $a->checkAuth()){
    $a->logout();
    $a->start();
}else{
	if($a->checkAuth()){ ///Start secured content
		$UID=$a->getAuthData('uid'); //get user ID
		$u = new User($UID);
	
		if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
			echo "Access denied! Subjects can't login in to the admin tool.";
		}else{
			$r = new Research($rid);
			$rname = $r->getName();
			if($r->users->isLocalAdmin($UID)){ //Check user level
				$u->unlock();
				//Resetting the timeout timer
				$a->setExpire($timeout, false);
				$uid = decrypt($_GET['id']);
				$us = new User($uid);
				$username = $us->getName();
				//Include the form needed for research creation.
				require_once("../functionality/editPrivar.php");
			}else{
				echo "Access denied,you are not allowed to browse this data.";
			}
		}
	}//end of secured content
}

//page footer
require_once("../UI/layout/bottom.php"); //Bottom part of the layout
?>