<?php
//Login form generation function include
require_once("../common/auth_loginf.php"); 
require_once("../common/auth_start.php");
//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php"); 
//Logout functionality, if logout flag set, perform logout. checkAuth()-checks if a session with valid authentication information exists 
	if($_GET['action'] == "logout" && $a->checkAuth() && !isset($_GET['sub'])){
		$UID=$a->getAuthData('uid'); //get user ID
		require_once("../common/includes.php"); //Class includes
    	$u = new User($UID);	// creates a new User object
    	$u->unlock();	// unlocks queries and researches
		$a->logout();	// perform a logout
    }else{
		if($a->checkAuth()){ ///Start secured content
			require_once("../common/includes.php"); //Class includes
			$UID=$a->getAuthData('uid'); //get user ID
			$u = new User($UID);
	
			if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
				echo "Access denied! Respondents can't login into the admin tool.";
				$a->logout();
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
				$u->unlock();
				//Resetting the timeout timer
				$a->setExpire($timeout, FALSE);
				//Including the index page contents
				require_once("../functionality/indexContent.php"); 
			}
		}//end secured content
	}//endif

	if(isset($_GET['sub'])&& $_GET['sub']==1){
		echo "<div class=\"access\">Access denied! Subjects can't login into the admin tool.</div>";
		$a->logout();
	}
	
//page footer
include("../UI/layout/bottom.php");
?>