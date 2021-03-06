<?php
/*
 * Adds Bluetooth IDs to all the respondents.
 * Calls ../functionality/addBtIdAll.php file to do the actual change.
 */
//Declaring the auth login form creation function and Starting authentication
require_once ("../common/auth_loginf.php");
require_once ("../common/auth_start.php"); 
require_once("../common/includes.php"); //Class includes
//bringing in the top part of the layout, if the login function didn't already do that

if($id==null){	
	$id=decrypt($_GET['id']);
}
$research_id = $_GET['id'];
$research_menu = true;
require_once("../UI/layout/top.php"); 
//Logout functionality, if logout flag set, perform logout.
if($_GET['action'] == "logout" && $a->checkAuth()) {
    $a->logout();
    $a->start();
}else{
	if($a->checkAuth()) { ///Start secured content
		$UID=$a->getAuthData('uid'); //get user ID
		$u = new User($UID);
		$r = new Research($id);
		if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
			echo "Access denied! Subjects can't login in to the admin tool.";
		}else{
			if($r->users->isLocalAdmin($UID)){ //Check user level
				$u->unlock();
				//Resetting the timeout timer
				$a->setExpire($timeout, false);
				//Include the form needed for research creation.
				echo "<div id=\"navpath\"><a href=\"index.php\"> Home </a> >> <a href=\"displayResearch.php?id=$research_id\"> ".$r->getName()." </a> >> <a href=\"#\"> Set bluetooth id's </a></div>";
				require_once("../functionality/addBtIdAll.php");
			}else{
				echo "Access denied,you are not allowed to browse this data.";
				echo '<p><a class="button" href="displayResearch.php?id='.$research_id.'"><span>Back</span></a></p>';
			}
		}
	}
}
//Bottom part of the layout
require_once("../UI/layout/bottom.php");
?>