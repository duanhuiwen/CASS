<?php
if($id==null){
	
 $id=decrypt($_GET['id']);}
//include("../common/includes.php");
if($id!=null){
	$user = new User($id);	
	$name= $user->getName();
	$roles = $user->getRoles();

	echo "<div id=\"navpath\"><a href=\"../content/index.php\"> Home </a> >> <a href=\"#\"> $name </a></div>";
	echo "<div id=\"descriptiondiv\">";
	echo("<div class=\"descrheader\"><div class=\"headertext\"><h1>$name</h1><hr /></div></div>");
	if($a->getAuthData('su_admin')==1){
		echo "<span class=\"backlink\"><a href=\"../content/admin.php\"><- Back</a></span>";
	}else{
		echo "<span class=\"backlink\"><a href=\"../content/index.php\"><- Back</a></span>";
	}
	echo "<div class=\"description\">";
	echo("<b>user ID: $id</b><br />");
	if($user->isSuperadmin() && $user->isResearchowner()){
		echo("<p>$name is a super admin and a research administrator</p>");
	}elseif($user->isResearchowner() && $user->isSuperadmin()==false){
		echo("<p>$name is a research administrator</p>");
	}elseif($user->isResearchowner()==false && $user->isSuperadmin()){
		echo("<p>$name is a super admin</p>");
	}
	echo "<h1 style=\"position:relative;left:-10px\">Users roles in researches</h1>";
		require_once("../functionality/listUsers.php");
		genUser("admin", $roles,$id);
		genUser("researcher", $roles,$id);
		genUser("subject", $roles,$id,$UID);
		if(count($roles)<1){
			echo "User is not participating any research";
		}
	// echo '<p><a class="button" href="./content/displayResearch.php?id='.$_GET['id'].'"><span>Back</span></a></p>';
	echo "</div></div>";
}else{
	echo("<h1>Error: No user specified!</h1>");
}

?>