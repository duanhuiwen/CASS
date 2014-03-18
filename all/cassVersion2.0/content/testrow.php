<?php
//Login form generation function include
require_once("../common/auth_loginf.php");
require_once("../common/auth_start.php");
require_once("../common/includes.php"); //Class includes

if($id==null){	
	$id=decrypt($_GET['id']);
}
$research_id = $_GET['id'];
$research_menu = true;
//bringing in the top part of the layout, if the login function didn't already do that


//Logout functionality, if logout flag set, perform logout.
if($_GET['action'] == "logout" && $a->checkAuth()){ 
    $a->logout();
    $a->start();
}else{
	if ($a->checkAuth()) { ///Start secured content

		$UID=$a->getAuthData('uid'); //get user ID
		$u = new User($UID);
		
		if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
			echo "Access denied! Subjects can't login in to the admin tool.";
		}else{
			$UID=$a->getAuthData('uid'); //get user ID
			//Resetting the timeout timer
			$a->setExpire($timeout, false);			
			if(isset($_GET['id'])){
				$rid = $id;
			}else{
				$rid=decrypt($_POST['id']);
				}
		
			
			$r = new Research($rid);
			$locked = $r->isLocked();
			if($r->users->isLocalAdmin($UID)){
				if($locked!="Ended" && $locked!="freezed"){
				$u->unlock();
				$sqlq= new UserSQLQueryer(); // creates a new UserSQLQueryer object
				$result= $sqlq->getAllUsers();
				
				while($row = mysql_fetch_assoc($result)){
					$localadmin = $sqlq->isLocalAdmin($rid,$row['UID']);
					$localres = $sqlq->isLocalResearcher($rid,$row['UID']);
					$localsub = $sqlq->isLocalSubject($rid,$row['UID']);
					$rights[$row['UID']] = array(
						'admin' => $localadmin,
						'researcher' => $localres,
						'subject' => $localsub
					);
					// Making a sort for the usernames, setting up anchors for the different name groups
					
			
					$sub = new Subject($row['UID']);
					$part = $sub->participatingIn(); // returns the ids of the research
					echo $part .'<br>';
					//echo $row['UID'].'<br>';
				//	echo mysql_numrows($part);
					}}}}}}
					

