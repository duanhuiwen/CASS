<?php
//Login form generation function include
require_once("../common/auth_loginf.php");
require_once("../common/auth_start.php");
//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php");

//Logout functionality, if logout flag set, perform logout.
	if($_GET['action'] == "logout" && $a->checkAuth()) {
   		$a->logout();
    	$a->start();
    }else{
		if($a->checkAuth()) { ///Start secured content
			require_once("../common/includes.php"); //Class includes
			$UID=$a->getAuthData('uid'); //get user ID
			$u = new User($UID);	
			if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
				echo "Access denied! Subjects can't login in to the admin tool.";
				if(!headers_sent()){
        			header('Location:index.php?action=logout&sub=1');
    			}else{
       				echo '<script type="text/javascript">';
				    echo 'window.location.href="index.php?action=logout&sub=1";';
				    echo '</script>';
				    echo '<noscript>';
				    echo '<meta http-equiv="refresh" content="0;url=index.php?action=logout&sub=1" />';
				    echo '</noscript>';
   	 			}
			}else{
				if($a->getAuthData('su_admin')==1){
					$u->unlock();
					//Resetting the timeout timer
					$a->setExpire($timeout, false);
					$id = $_GET['id'];
					$action = $_GET['action'];
					//Including the page contents
					require_once("../functionality/adminIndexContent.php");

				}
			}
		}//end secured content
	}//endif

//page footer	
echo("<br /><br /><br /><br /><br /><br /><br /><br />");  
include("../UI/layout/bottom.php");

?>