<?php
require_once("../common/auth_loginf.php"); //Login form generation function include
require_once("../common/auth_start.php");

//require_once("../UI/layout/top.php"); //bringing in the top part of the layout, if the login function didn't already do that

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
				if(isset($_GET['picID'])){
					$an = new Answer($_GET['picID']);
					$r = new Research($an->getResearchID());
					if($r->users->isLocalAdmin($UID) || $r->users->isLocalResearcher($UID)){
						//Resetting the timeout timer
						$a->setExpire($timeout, false);
						$f = new FileIOHandler();
						$type=$_GET['type'];
						if($type==7){
							header('Content-type:image/jpeg');
						}elseif($type==8){
							header('Content-type:video/3gpp');
						}elseif($type==3){
							header('Content-type:audio/amr');
						}	
						echo $f->MediaRead($type,$_GET['picID']);
					}else{
						echo "Access denied!";
					}	
				}
			}//end secured content
		}//endif
    }
?>