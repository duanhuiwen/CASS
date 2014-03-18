<?php
//Declaring the auth login form creation function and Starting authentication
require_once ("../common/auth_loginf.php");
require_once ("../common/auth_start.php");
require_once("../common/includes.php"); //Class includes
if($id==null){	
	$id=decrypt($_GET['id']);
}
$research_id = $_GET['id'];
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
			//Resetting the timeout timer
			$a->setExpire($timeout, false);
			if(isset($_GET['id'])){
				$research = new Research($id);
				$rname = $research->getName();
				$method = $research->getCollMethod();
				$desc = $research->getDescr();
				$start = $research->getStartTime();
				/*$start = explode("-",$start);
				$osy = $start[0];
				$osm = $start[1];
				$osd = $start[2];*/
				$end = $research->getEndTime();
				/*$end = explode("-",$end);
				$oey = $end[0];
				$oem = $end[1];
				$oed = $end[2];*/
				$qPerDay = $research->getQueriesPerDay();
				if($method==0){
					$queryTimes = $research->getQueryTimes();
				}
				if($id!=null){
						if($research->users->isLocalAdmin($UID)){
							if(isset($_GET['action']) && $_GET['action']=="rm"){
								if($research->rmResearch()){
									if(!headers_sent()){
        								header('Location:index.php');
    								}else{
       									echo '<script type="text/javascript">';
				   						echo 'window.location.href="index.php";';
				    					echo '</script>';
									    echo '<noscript>';
									    echo '<meta http-equiv="refresh" content="0;url=index.php" />';
									    echo '</noscript>';
					   	 			}
								}
							}else{
								require_once("../UI/forms/editResearch.php");//Include the form needed for query creation.
							}
						}else{
							echo "Access denied,you are not allowed to browse this data.";
						}
					
				}else{
					echo("Error: No query specified!");
				}
			}
		}
	}
}	
require_once("../UI/layout/bottom.php"); //Bottom part of the layout
?>