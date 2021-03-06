<?php
require_once("../common/auth_loginf.php"); //Login form generation function include
require_once("../common/auth_start.php");	// Authentication include
require_once("../common/includes.php"); //Class includes
if($id==null){	
	$id=decrypt($_GET['id']);
}
$research_id = $_GET['id'];
$research_menu = true;	// a variable that the function in top.php would start to build up the Research menu in the left div

// bringing in the top part of the layout, if the login function didn't already do that
// This file builds up the left div as well.
require_once("../UI/layout/top.php");

	if ($_GET['action'] == "logout" && $a->checkAuth()) {
   	 	$a->logout();
   	 	$a->start();
    }else{
		if ($a->checkAuth()) { ///Start secured content
			
			$UID=$a->getAuthData('uid'); //get user ID
			$u = new User($UID);	
			
			if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
				echo "<p>Access denied! Subjects can't login in to the admin tool.</p>";
			}else{
				$r = new Research($id);
				if($r->users->isLocalAdmin($UID) || $r->users->isLocalResearcher($UID)){
					$u->unlock();
					
					include("../functionality/displayResearch.php");//This includes the actual content of the page
				}else{
					echo "<p>Access denied, you are not allowed to browse this data.</p>";
				}
			}
		}//end secured content
	}//endif

//Content of page footer
echo("<br /><br />");   
include("../UI/layout/bottom.php");
?>