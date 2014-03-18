<?php
//Login form generation function include
require_once("../common/auth_loginf.php"); 
require_once("../common/auth_start.php");
require_once("../common/includes.php"); //Class includes
//for menu generation
if(isset($_POST['id'])){	
	$id=decrypt($_POST['id']);
	$research_id = $_POST['id'];
}else{
	$id = decrypt($_GET['id']);
	$research_id = $_GET['id'];
}

$research_menu = true;
//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php"); 

if ($_GET['action'] == "logout" && $a->checkAuth()) { //Logout functionality, if logout flag set, perform logout.
    $a->logout();
	$a->start();
}else{
	if ($a->checkAuth()) { ///Start secured content
		$UID=$a->getAuthData('uid'); //get user ID
		$u = new User($UID);
	
		if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
			echo "Access denied! Subjects can't login in to the admin tool.";
		}else{
			//Resetting the timeout timer
			$a->setExpire($timeout, false);	
				
			$r= new Research($id);
				if($r->users->isLocalAdmin($UID)){
					$u->unlock();
					echo "<div id=\"navpath\"><a href=\"index.php\"> Home </a> >> <a href=\"displayResearch.php?id=$research_id\"> ".$r->getName()." </a> >> <a href=\"#\"> Add respondents </a></div>";
					require_once("../functionality/addnsubjects.php");
				}else{
					echo "Access denied,you are not allowed to browse this data.";
				}
		}
	}//end secured content
}//endif

//Page footer
include("../UI/layout/bottom.php");
?>