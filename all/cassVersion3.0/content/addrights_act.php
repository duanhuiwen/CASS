<?php
require_once("../common/auth_loginf.php"); //Login form generation function include
require_once("../common/auth_start.php");
require_once("../common/includes.php"); //Class includes
if($id==null){
	$id=decrypt($_POST['id']);
}
$research_id = $_POST['id'];
//$research_menu = true;
require_once("../UI/layout/top.php"); //bringing in the top part of the layout, if the login function didn't already do that



if ($_GET['action'] == "logout" && $a->checkAuth()) { //Logout functionality, if logout flag set, perform logout.
    $a->logout();
    $a->start();
}else{
	if ($a->checkAuth()) { ///Start secured content
		if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0){
			echo "Access denied! Subjects can't login in to the admin tool.";
		}else{
			$UID=$a->getAuthData('uid'); //get user ID
			$a->setExpire($timeout, false);			//Resetting the timeout timer
			/*if(isset($_GET['id'])){
				$rid = $_GET['id'];
			}else{
				$rid=$_POST['id'];
			}*/
			$r= new Research($id);
			if($r->users->isLocalAdmin($UID)){
				$num= count($_POST);
				$keys=array_keys($_POST);
				$rights = $_POST['rights'];
				if(isset($_POST)){	
					for($i=0; $i<$num; $i++){
						//$pieces = explode(";",decrypt($keys[$i]));
						$pieces = explode(";",$keys[$i]);
						//var_dump($pieces);
						if(is_numeric($pieces[0])){
							$uid = encrypt($pieces[0]);
							$uid2 = $pieces[0];
						}
						if($pieces[1]!=null){
							$right = $pieces[1];
						}
						
						switch($right){
							case "admin":{
								//if($_POST[encrypt($uid2.";admin")]==1){
								if($_POST[$uid2.";admin"]==1){
									if($rights[$uid]['admin']==false){
										$r->users->addAdmin($uid2);
										//echo("Admin added for $uid2<br />");
									}
								}else{
									if($rights[$uid]['admin']){
										$r->users->rmAdmin($uid2);
										//echo "<br />removed admin $uid2<br />";
									}
								}
							break;
							}						
							case "researcher":{
								//if($_POST[encrypt($uid2.";researcher")]==1){
								if($_POST[$uid2.";researcher"]==1){	
									if($rights[$uid]['res']==false){
										$r->users->addResearcher($uid2);
										//echo("Researcher added for $uid2<br />");
									}
								}else{
									if($rights[$uid]['res']){
										$r->users->rmResearcher($uid2);
										//echo "removed researcher $uid2<br />";
									}
								}
							break;
							}						
							case "subject":{
								//if($_POST[encrypt($uid2.";subject")]==1){
								if($_POST[$uid2.";subject"]==1){
									if($rights[$uid]['sub']==false){
										$sub = new Subject($uid2);
										$part = $sub->participatingIn();
										if(mysql_numrows($part)>0){
											for($j=0;$j<mysql_numrows($part);$j++){
												$partRid = mysql_result($part,$j,'research_id');
												if($r->checkOverlap($partRid)){
													$overlaps = true;
													break;
												}else{
													$overlaps = false;
												}
											}
										}else{
											$overlaps=false;
										}
									
											if($overlaps!=1){
												$r->users->addSubject($uid2);
												//echo("Subject added for $uid2<br />");
											}
										}
								}else{
									if($rights[$uid]['sub']){
										$r->users->rmSubject($uid2);
										//echo "removed subject $uid2<br />";
									}
								}
							break;
							}
						}
						
					}
					//Redirect to displayResearch page
					if(!headers_sent()){
		       			header('Location:displayResearch.php?id='.$research_id);
			    	}else{
			        	echo '<script type="text/javascript">';
			        	echo 'window.location.href="displayResearch.php?id='.$research_id.'";';
			        	echo '</script>';
			        	echo '<noscript>';
			        	echo '<meta http-equiv="refresh" content="0;url=displayResearch.php?id='.$research_id.'" />';
			        	echo '</noscript>';
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

//####SISÄLLÖN LOPPU ######
include("../UI/layout/bottom.php");
?>