<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fi" lang="fi">
	<head>
		<title>Cass-Q Admin 2 v. 0.9-b (12.9.28)</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="../UI/layout/cass.css" type="text/css" />
		<link rel="stylesheet" type="text/css" href="../UI/layout/dropdown.css" />
		<link rel="stylesheet" type="text/css" href="../UI/layout/smoothness/jquery-ui-1.7.1.custom.css" />
		<script type="text/javascript" src="../AjaxAdd/functions.js"></script>
		<script src="../AjaxAdd/jquery-1.3.2.min.js" type="text/javascript"></script>
		<script src="../AjaxAdd/jquery-ui-1.7.1.custom.min.js" type="text/javascript"></script>
		<script type="text/javascript" src="../AjaxAdd/jquery.disable.text.select.js"></script>
		<link rel="shortcut icon" href="../UI/layout/favicon.ico" type="image/x-icon" />
	</head>
	<body>
		<div id="wrapper">
			<div id="banner">
				<img src="../UI/layout/cass.gif" width="761" height="236" alt="CASS-Q Administrator" />
			</div>
			
			<?php 
			//Login form generation function include
			require_once("../common/auth_loginf.php"); 
			require_once("../common/auth_start.php");
			if(isset($a)){
				echo "<script src=\"../AjaxAdd/dropDownFunctions.js\" type=\"text/javascript\"></script>";
				// Logout functionality, if logout flag set, perform logout.
				if($_GET['action'] == "logout" && $a->checkAuth()){
					$UID=$a->getAuthData('uid'); //get user ID
					require_once("../common/includes.php"); //Class includes
			    	$u = new User($UID);
			    	$u->unlock();
			   	 	$a->logout();
			   	 	if(!isset($_GET['sub'])){
			   	 		$a->start();
			   	 	}
					if(!headers_sent()){
			        	header('Location:index.php');
			    	}else{
			       		echo '<script type="text/javascript">';
						echo 'window.location.href="index.php";';
						echo '</script>';
						echo '<noscript>'; // provide an alternate content for a browser that doesn't support client-side scripting
						echo '<meta http-equiv="refresh" content="0;url=index.php" />'; // supposed to go into the header, refreshes the page in 0 sec, and goes to index.php
						echo '</noscript>';
			   	 	}
				}else{
					if($a->checkAuth()){ ///Start secured content
						require_once("../common/includes.php"); //Class includes
						$UID=$a->getAuthData('uid'); //get user ID
						$u = new User($UID);
						if($a->getAuthData('su_admin')==1 || $a->getAuthData('research_owner')==1 || $u->hasRightToLoginIn()==true){
			?>

			<div id="headerMenu">
			<!-- Drop down menu headers -->
				<ul id="nav">
					<li class="top"><a href="../content/index.php" id="mainmenu" class="top_link"><span class="down">Main menu</span></a>
						<ul class="sub">
							<li><a href="../content/index.php" class="fly">Research(es)</a>
								<ul>				
									<?php 
										$sql=new ResearchSQLQueryer();
										$rList=$sql->listResearchByUser($UID);
										if(mysql_numrows($rList)>0){
											for($i=0;$i<mysql_numrows($rList);$i++){
												$reid = mysql_result($rList,$i,"research_id");
												$rname = mysql_result($rList,$i,"research_name");
												$rname = substr($rname,0,12);
												$rname .="...";
												$re = new Research($reid);
												if($re->users->isLocalAdmin($UID) || $re->users->isLocalResearcher($UID)){
													echo "<li><a href=\"../content/displayResearch.php?id=".encrypt($reid)."\" class=\"fly\">$rname</a>";
													$qu = $sql->getQueries($reid);
													echo "<ul>";
													if(mysql_numrows($qu)>0){											
														echo "<li>Queries</li>";										
														for($j=0;$j<mysql_numrows($qu);$j++){
															$quid = mysql_result($qu,$j,"query_id");
															$qname = mysql_result($qu,$j,"name");
															$qname = substr($qname,0,12);
															$qname .="...";
															if($re->users->isLocalAdmin($UID)){
																echo "<li><a href=\"../content/displayQuery.php?id=".encrypt($quid)."\" class=\"fly\">$qname</a>";
																	echo "<ul>";
																		echo "<li>Actions</li>";
																		echo "<li><a href=\"../content/manipQuery.php?id=".encrypt($quid)."\">Edit Questions</a></li>";
																		echo "<li><a href=\"../content/editQuery.php?id=".encrypt($quid)."\">Edit Query</a></li>";
																	echo "</ul>";	
																echo "</li>";
															}else{
																echo "<li><a href=\"../content/displayQuery.php?id=".encrypt($quid)."\">$qname</a></li>";
															}												
														}
													}
													if($re->users->isLocalAdmin($UID)){
														echo "<li>Actions</li>";
														echo "<li><a href=\"../content/editResearch.php?id=".encrypt($reid)."\">Edit Research</a></li>";
														echo "<li><a href=\"../content/createQuery.php?id=".encrypt($reid)."\">Add Query</a></li>";
														//echo "<li><a href=\"../content/addrights.php?id=".encrypt($reid)."\">Edit User Rights</a></li>";
														echo "<li><a href=\"../content/addnsubjects.php?id=".encrypt($reid)."\">Add Respondents</a></li>";
													}
														echo "</ul>";									
														echo "</li>";
												}
											}
											echo "</ul>";
											echo "</li>";
										}else{
											echo "<li>No researches</li></ul>";
										}
						echo "</ul>";
					echo "</li>";	
					if($a->getAuthData('su_admin')==1 || $a->getAuthData('research_owner')==1){
									?>						
				<li class="top"><a href="#nogo1" class="top_link"><span class="down">Add</span></a>
					<ul class="sub">
					<?php if($a->getAuthData('research_owner')==1){ ?>
						<li><a href="../content/addResearch.php">Add Research</a></li>
						<?php } ?>
						<li><a href="../content/addUser.php">Add User</a></li>
					</ul>
				</li>
				<?php } ?>
			</ul>
		</div>
		<div id="logout">
			<!--  <span class="preload1"></span>
			<span class="preload2"></span> -->
			<ul id="nav">
				<li class="top"><a href="../content/profile.php" id="profile" class="top_link"><span>Your profile</span></a></li>
				<li class="top"><a href="../content/index.php?action=logout" id="profile" class="top_link"><span>Logout</span></a></li>
			</ul>
		</div>	
			<?php }
			}
		}
	} ?>
			 	 
		<div id="left">
			<?php
				if($a!=null && $a->checkAuth() && $_GET['action']!='logout'){
					require("../UI/menuGen.php");	// calls the MenuGenerator class
					$menu = new MenuGenerator();
					$menu->MainMenu();	// builds the Main Menu part in the left div
					if($a->getAuthData('su_admin')==1 || $a->getAuthData('research_owner')==1){	// if the user has enough right adds more menu items
						$menu->AdminMenu($a->getAuthData('su_admin'), $a->getAuthData('research_owner'));	// Administration menu
						if($a->getAuthData('su_admin')==1){
							$menu->SuperAdminMenu($a->getAuthData('su_admin'));	// if super admin, adds super admin menu
						}
						
					}
					if($research_menu){	// if the research_menu variable set to true then it shows this part too
						$researchi = new Research(decrypt($research_id));
						$UID=$a->getAuthData('uid');
						$menu->ResearchMenu($research_id, $researchi->users->isLocalAdmin($UID));
					}
				} else {
				echo '<p>Cass Query client for mobile phone:</p>';
				echo '<p><a href="../installation/CassQ.jad">Download client</a></p>';
				}
			?>
		</div><!-- end left -->
   		<div id="content">
