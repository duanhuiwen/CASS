<?php
if($a->getAuthData('research_owner')==1||$a->getAuthData('su_admin')==1){
	//form for adding users
	echo "<div id=\"adduserdiv\">";
	echo "<div class=\"descrheader\"><div id=\"help\" onclick=\"openhelp('newuser');\">?</div><div class=\"headertext\"><h1> Add new user</h1><hr /></div></div>";
	$tmp=require_once("../UI/forms/addUser.php");
	$admin=$a->getAuthData('su_admin');
	$rowner=$a->getAuthData('research_owner');
	
	createAddUserTable($admin, $rowner);
		
	echo "</div>";
}else{
	echo("<h2> You are not allowed to add new users.</h2>");
}

?>