<?php
//Declaring the auth login form creation function and Starting authentication
require_once ("../common/auth_loginf.php"); 
require_once ("../common/auth_start.php"); 
require_once("../common/includes.php"); //Class includes

$researchID=$_GET['id'];
if($id==null){	
	$id=decrypt($_GET['id']);
}
$research_id = $_GET['id'];
$research_menu = true;
//bringing in the top part of the layout, if the login function didn't already do that
require_once("../UI/layout/top.php");

	$UID=$a->getAuthData('uid'); //get user ID
	$u = new User($UID);
	
	if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
		echo "Access denied! Subjects can't login in to the admin tool.";
	}else{
		//Check user level
		$r = new Research($id);
		if($r->users->isLocalAdmin($UID)){
			$u->unlock();
			//Resetting the timeout timer
			$a->setExpire($timeout, false);
			$colmet = $r->getCollMethod();
			$qPerDay = $r->getQueriesPerDay();
			$queries = mysql_numrows($r->listChildren());
			if((($colmet==0 || $colmet==1) && $qPerDay==$queries) || (($colmet==2 || $colmet==3) && $queries>0)){
				echo "<h1>Cannot insert more queries,these queries will never be sent!</h1>";
			}else{
				//Include the form needed for query creation.
				echo "<div id=\"navpath\"><a href=\"index.php\"> Home </a> >> <a href=\"displayResearch.php?id=$research_id\"> ".$r->getName()." </a> >> <a href=\"#\"> Add new query </a></div>";
				require_once("../UI/forms/createQuery.php");
			}
		}else{
			echo "Access denied,you are not allowed to browse this data.";
		}
	}
//page footer
require_once("../UI/layout/bottom.php"); //Bottom part of the layout
?>