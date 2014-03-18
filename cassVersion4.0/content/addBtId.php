<?php
/*
 * This file adds the Bluetooth id to the system in connection with a respondent.
 */
//Login form generation function include
require_once("../common/auth_loginf.php"); 
require_once("../common/auth_start.php");
require_once("../common/includes.php"); //Class includess
//bringing in the top part of the layout, if the login function didn't already do that
$research_id=$_GET['rid'];
$rid = decrypt($_GET['rid']);
$id = decrypt($_GET['id']);
$research_menu = true;
require_once("../UI/layout/top.php"); 

	if ($_GET['action'] == "logout" && $a->checkAuth()) {
    	$a->logout();
    	$a->start();
    }else{
		if ($a->checkAuth()) { ///Start secured content
			$UID=$a->getAuthData('uid'); //get user ID
			$u = new User($UID);
			$r = new Research($rid);
			if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
				echo "Access denied! Subjects can't login in to the admin tool.";
			}else{
				if($a->getAuthData('su_admin')==1 || $r->users->isLocalAdmin($UID)){
					//This includes the actual content of the page
					include("../functionality/addBtId.php");
				}
			}
		}//end secured content
	}//endif

//page footer	
echo("<br /><br />");    
include("../UI/layout/bottom.php");

?>