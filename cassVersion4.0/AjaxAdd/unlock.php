<?php
/*
 * Used to unlock research and/or query and the user.
 */
require_once("../common/includes.php"); //Class includes
if(isset($_GET['uid']) && isset($_GET['id']) && isset($_GET['type'])){
	$uid = decrypt($_GET['uid']);
	$id = decrypt($_GET['id']);
	if($_GET['type']==1){
		$r = new Research($id);
		$locked = $r->isLocked();
	}elseif($_GET['type']==2){
		$q = new Query($id);
		$rid = $q->getOwner();
		$r = new Research($rid);
		$locked = $q->isLocked();
	}
	if($r->users->isLocalAdmin($uid) && $locked==$uid){
		$u = new User($uid);
		$u->unlock();
	}
}elseif(isset($_GET['uid']) && isset($_GET['id']) && isset($_GET['action'])){
	$uid = decrypt($_GET['uid']);
	$id = decrypt($_GET['id']);
	if($_GET['action']=="freeze"){
		$r = new Research($id);
		if($r->users->isLocalAdmin($uid)){
			echo $r->freezeResearch();
		}
	}
}

?>