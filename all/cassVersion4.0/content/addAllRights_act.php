<?php
/*
 * This file can change the users role in the system. It can grant super administrator
 * or research administrator role to the user.
 */
//Login form generation function include
require_once("../common/auth_loginf.php");
require_once("../common/auth_start.php");
if($id==null){
	$id=$_GET['id'];
}

//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php");
//Logout functionality, if logout flag set, perform logout.
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
			if($a->getAuthData('su_admin')==1){
				//Resetting the timeout timer
				$a->setExpire($timeout, false);			
				if(isset($_GET['id'])){
					$userid = $_GET['id'];
				}else{
					$userid=$_POST['id'];
				}
				$u=new User($userid);
				$suAdmin = $_POST['suadmin'];
				$rOwn=$_POST['researchO'];
				if(isset($_POST)){	
					if($suAdmin==1){
						if($u->isSuperadmin()==false){
							if($u->addSuperARight()){
								//echo "Super Admin right added from user: $userid";
								$success = true;
							}
						}
					}else{
						if($u->isSuperadmin()){
							if($u->rmSuperARight()){
								//echo "Super Admin right removed from user: $userid";
								$success = true;
							}
						}
					}
					if($rOwn==1){
						if($u->isResearchowner()==false){
							if($u->addROwnerRight()){
								//echo "Research owner right added from user: $userid";
								$success = true;
							}
						}
					}else{
						if($u->isResearchowner()){
							if($u->rmROwnerRight()){
								//echo "Research owner right removed from user: $userid";
								$success = true;
							}
						}
					}
				if($success==true){
					if(!headers_sent()){
						header('Location:../content/editUser.php?id='.$userid.'');
					}else{
					    echo '<script type="text/javascript">';
						echo 'window.location.href="../content/editUser.php?id='.$userid.'";';
						echo '</script>';
						echo '<noscript>';
						echo '<meta http-equiv="refresh" content="0;url=../content/editUser.php?id='.$userid.'" />';
						echo '</noscript>';
					}
				}
					
				}else{
					echo "Post not set!";
				}			
			}else{
				echo "Access denied,you are not allowed to browse this data.";
			}
		}
	}//end secured content
}//endif

//page footer
include("../UI/layout/bottom.php");
?>