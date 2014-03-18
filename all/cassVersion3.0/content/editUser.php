<?php
require_once ("../common/auth_loginf.php"); //Declaring the auth login form creation function
require_once ("../common/auth_start.php"); //Starting authentication
require_once("../UI/layout/top.php"); ////bringing in the top part of the layout, if the login function didn't already do that

if($a->getAuthData('su_admin')==1){ //Check user level
	$a->setExpire($timeout, false);			//Resetting the timeout timer
	require_once("../common/includes.php");
	if(isset($_GET['id'])){
	$id=$_GET['id'];
	if(!is_numeric($id)){
		$id=decrypt($id);	
	}
	$u = new User($id);
	$uname = $u->getName();
	$roles = $u->getRoles();
	require_once("../functionality/editUser.php");//Include the form needed for query creation.
	}else{
		echo "Error!";
	}	
}
require_once("../UI/layout/bottom.php"); //Bottom part of the layout
?>