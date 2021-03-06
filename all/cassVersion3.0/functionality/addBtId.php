<?php
/*
 * This file adds the Bluetooth id to a respondent.
 * The form calls the addBtID_act.php file.
 */
	if($id!=null){
		$user = new User($id);	
		$name= $user->getName();
		$roles = $user->getRoles();
		$bt_id = $user->subjects->getBt_id($rid);
		$rname = $r->getName();
		
		for($j=0;$j<count($roles);$j++){
			if($roles[$j]['subject'] == 1){	
				$subject = true;
				break;
			}
		}
		if($subject==true){
			echo "<div id=\"navpath\"><a href=\"../content/index.php\"> Home </a> >> <a href=\"../content/displayResearch.php?id=$research_id\"> $rname </a> >> <a href=\"../content/showuser.php?id=".encrypt($id)."\"> $name </a> >> <a href=\"#\"> Add Bluetooth ID </a></div>";
			echo "<div id=\"descriptiondiv\">";
			echo "<div class=\"descrheader\"><div class=\"headertext\"><h1>Set bluetooth id</h1><hr /></div></div>";
			echo "<div class=\"description\">";
			echo "<form id=\"setbtidform\" method=\"post\" action=\"addBtId_act.php\">";
			echo "Set respondents <b>$name</b> bluetooth id for research <b>$rname</b><br /><br />";
			echo "<tt>In Nokia phones you get the bluetooth id by entering *#2820# in the phone.</tt><br /><br />";
			echo "Bluetooth id: <input type=\"text\"  id=\"btid\" name=\"btid\" size=\"12\" value=\"$bt_id\" maxlength=\"12\" />";
			echo "<input type=\"hidden\" name=\"uid\" value=\"".$_GET['id']."\" />";
			echo "<input type=\"hidden\" name=\"rid\" value=\"".$_GET['rid']."\" />";
			echo "<br /><br /><a class=\"button\" href=\"#\" onclick=\"this.blur();validateBtid()\"><span>Set</span></a>";
			echo "</form>";
			echo "</div></div>";
		}
}else{
	echo("<h1>Error: No user specified!</h1>");
}

?>