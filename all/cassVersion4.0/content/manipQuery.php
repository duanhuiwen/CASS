<?php
require_once("../common/auth_loginf.php"); //Login form generation function include
require_once("../common/auth_start.php");
require_once("../common/includes.php"); //Class includes

if($id==null){	
	$id=decrypt($_GET['id']);
}
$qid=decrypt($_GET['id']);
$query = new Query($qid);
$research_id = encrypt($query->getOwner());
$research_menu = true;
//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php");

	if ($_GET['action'] == "logout" && $a->checkAuth()) {
    	$a->logout();
    	$a->start();
    }else{
    if ($a->checkAuth()) { ///Start secured content
			$UID=$a->getAuthData('uid'); //get user ID
			$u = new User($UID);
			if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
				echo "Access denied! Subjects can't login in to the admin tool.";
			}else{	
				$usn=$a->getUsername();
				$action = $_GET['action'];
				if($qid!=null){
					$rid=$query->getOwner();	// getting the researcher's id
					$research = new Research($rid);
					// If the requested action was to remove the query
					if($action=="rm" && $query->isLocked()==false && $research->isLocked()==false){
						if($research->users->isLocalAdmin($UID)){
							if($query->rmQuery()){	// If removing the query was sucessful
								if(!headers_sent()){
        							header('Location:displayResearch.php?id='.encrypt($rid));
    							}else{
				       				echo '<script type="text/javascript">';
								    echo 'window.location.href="displayResearch.php?id='.encrypt($rid).'";';
								    echo '</script>';
								    echo '<noscript>';
								    echo '<meta http-equiv="refresh" content="0;url=displayResearch.php?id='.encrypt($rid).'" />';
								    echo '</noscript>';
   	 							}
							}
						}else{
							echo "Access denied, you are not allowed to do this action.";
						}
					}else{
						// Checking that user has permission and if so including content  
						if($research->users->isLocalAdmin($UID)){
							if($research->isLocked()==$UID){
								$research->unLock($UID);
							}
							include("../AjaxAdd/manipQuery.php"); //TODO: Change the name, and turn this into something more sensible
						}else{
							echo "Access denied,you are not allowed to browse this data.";
						}
					}
				}else{
					echo("Error: No query specified!");
				}
		}
	}//end secured content
}//endif

//page footer
echo("<br /><br /><br /><br /><br /><br /><br /><br />");    
include("../UI/layout/bottom.php");

?>