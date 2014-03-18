<?php
 //Login form generation function include
require_once("../common/auth_loginf.php");
require_once("../common/auth_start.php");
require_once("../common/includes.php"); //Class includes
$id=decrypt($_POST['qid']);
	//Lets create a new research object
	$query = new Query($id);
	$rid = $query->getOwner();
	$res = new Research($rid);
	//$research_id = encrypt($rid);
	//$research_menu = true;
//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php");

$UID=$a->getAuthData('uid'); //get user ID
$u = new User($UID);
	
if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
	echo "Access denied! Subjects can't login in to the admin tool.";
}else{
	if($res->users->isLocalAdmin($UID)){		
		//Include the form needed for Query creation.
		require_once("../functionality/updateQuery.php");
		//Resetting the timeout timer
		$a->setExpire($timeout, false);
	}
}//end of secured content

//page footer
require_once("../UI/layout/bottom.php");
?>