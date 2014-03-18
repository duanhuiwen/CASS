<?php
/*
 * This file is for showing pictures, sounds and videos collected as answers. It uses
 * the ./functionality/getAnswers.php file.
 */
require_once("../common/auth_loginf.php"); //Login form generation function include
require_once("../common/auth_start.php");	// Creates an Auth object
require_once("../common/includes.php"); //Class includes
$picID = $_GET['picID']; // gets the picID
$aw = new Answer($_GET['picID']); // creates a new Answer object
$research_id = encrypt($aw->getResearchID()); // gets the research id and encrypt it
$research_menu = true;	// let the menu to be drawn on the page
require_once("../UI/layout/top.php"); //bringing in the top part of the layout, if the login function didn't already do that

//Logout functionality, if logout flag set, perform logout.
	if($_GET['action'] == "logout" && $a->checkAuth()){
    	$a->logout();
    	$a->start();
    }else{
		if($a->checkAuth()){ ///Start secured content
			$UID=$a->getAuthData('uid'); //get user ID
			$u = new User($UID); // creates a new user object
			// if the user does not have enough rights, cannot log in
			if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
				echo "Access denied! Subjects can't login in to the admin tool.";
			}else{
				//Resetting the timeout timer
				$a->setExpire($timeout, false);
				if(isset($_GET['picID'])){
					$picID = $_GET['picID'];
					$r = new Research($aw->getResearchID()); // creates a new research object
					if($r->users->isLocalAdmin($UID) || $r->users->isLocalResearcher($UID)){
						$f = new FileIOHandler();
						$type = $aw->getType();
						if($f->getFiles2Db($_GET['picID'])){
							echo "Media File: <br />";
							if($type==7){
								echo "<img src=\"../functionality/showpic.php?picID=$picID&type=$type\" width=\"400\" height=\"300\"></img>";
							}else{
								echo "<embed src=\"../functionality/showpic.php?picID=$picID&type=$type\" autostart=\"true\"></embed>";
							}
							echo "<br />File: ".$aw->getMediaFileName()."<br /><br />";
							echo "<form action=\"../functionality/getAnswers.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"rid\" value=\"".encrypt($aw->getResearchID())."\" />";
							echo "<input type=\"hidden\" name=\"aid\" value=\"".encrypt($_GET['picID'])."\" />";
							echo "<input type=\"submit\" name=\"mediadownload\" value=\"Download\" />";
							echo "</form>";
						}else{
							$filepath = $aw->getMediaFilePath();
							$info=pathinfo($filepath);
							$filename=$info['basename']; 
							echo $f->MediaRead($type,$_GET['picID']);
							echo "<br />File:$filename<br /><br />";
							echo "<form action=\"../functionality/getAnswers.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"rid\" value=\"".encrypt($aw->getResearchID())."\" />";
							echo "<input type=\"hidden\" name=\"aid\" value=\"".encrypt($_GET['picID'])."\" />";
							echo "<input type=\"submit\" name=\"mediadownload\" value=\"Download\" />";
							echo "</form>";
						}		
					}
				}else{
					echo "No file available";
				}
				
		}
	}//end secured content
}//endif

//page footer
echo("<br /><br /><br /><br /><br /><br /><br /><br />");
include("../UI/layout/bottom.php");
?>