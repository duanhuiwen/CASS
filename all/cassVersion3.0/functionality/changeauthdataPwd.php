<?php
if($a->getAuthData('su_admin')==1){
	echo "<div id=\"descriptiondiv\">";
	echo("<div class=\"descrheader\"><div class=\"headertext\"><h1>Change password for user</h1><hr /></div></div>");
	echo "<span class=\"backlink\"><a href=\"../content/editUser.php?id=$id\"><- Back</a></span>";
	echo "<div id=\"invalidform\"><div class=\"errortxt\"></div></div>";
	echo "<div class=\"description\">";
	$tmp=require_once("../UI/forms/addUser.php");
	$admin=$a->getAuthData('su_admin');
	$rowner=$a->getAuthData('research_owner');
	$u = new User($id);
	$usn = $u->getName();
	
	createChangepwdTableForSuper($id,$usn);
	
	echo("</table>");
	echo "</div></div>";
	}//endif
	else{
		echo("<h2> You are not allowed to add new users.</h2>");
	}
?>