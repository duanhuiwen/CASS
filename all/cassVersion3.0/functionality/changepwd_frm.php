<?php
if($a->getAuthData('research_owner')==1||$a->getAuthData('su_admin')==1 || $u->hasRightToLoginIn()){
	echo "<div id=\"adduserdiv\">";
	echo "<div class=\"descrheader\">";
	echo("<div class=\"headertext\"><h1>Change password</h1><hr /></div></div>");
	$tmp=require_once("../UI/forms/addUser.php");
	$admin=$a->getAuthData('su_admin');
	$rowner=$a->getAuthData('research_owner');
	$usn=$a->getUsername();
	//generate form 
	createChangepwdTable($UID,$usn);

	echo("</table>");
	echo "</div>";
}else{
	echo("<h2> You are not allowed here</h2>");
}

?>