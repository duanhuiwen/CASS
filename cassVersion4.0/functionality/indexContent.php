<?php
if($u->hasRightToLoginIn()){
	$usn=$a->getUsername();
	$date=date('d.m.Y');	

	echo "<div id=\"descriptiondiv\">";
	echo "<div class=\"descrheader\"><div class=\"headertext\"><h1>Welcome $usn </h1><hr /></div></div>";
	echo "<div class=\"description\">";
	echo("<div class=date><h2>Today is $date</h2></div>");

	if($a->getAuthData('su_admin')==1){ //Check user level
		echo("<p>You are a Super Administrator<br /></p>");
	}

	if($a->getAuthData('research_owner')==1){ //Check user level
		echo("<p>You are a Research Administrator<br />"); 
	}
	//List research user participates in, if a Researcher or a subject
	UserResearchList($UID);
	echo "</div></div>";
}
?>