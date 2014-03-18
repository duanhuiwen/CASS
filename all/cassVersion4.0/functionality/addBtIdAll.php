<?php
/*
 * Intermediate file to add BT IDs to the respondents.
 * The form calls the ../content/addBtIdAll_act.php file.
 */
if($r->users->isLocalAdmin($UID)){
	$locked = $r->isLocked();
	if($locked!="Ended" && $locked!="freezed"){
		echo "<div id=\"addsubsdiv\">";
		echo "<div class=\"descrheader\"><div class=\"headertext\"><h1>Set bluetooth id to respondents</h1><hr /></div></div>";
		echo("<div class=\"addsubcont\">Research: ".$r->getName()."");
		echo "<br /><br />";
		$subs = $r->users->getSubjects();
		if(mysql_numrows($subs)){
			echo "<div id=\"invalidform\"><div class=\"errortxt\"></div></div>";
			echo "<form id=\"addbtidallform\" name=\"addbtidallform\" method=\"post\" action=\"../content/addBtIdAll_act.php\">";
			echo "<tt>In Nokia phones you get the bluetooth id by entering *#2820# in the phone.</tt><br /><br />";
			echo "<div class=\"btinputheading\"><b>Bluetooth ID</b></div>";
			for($i=0;$i<mysql_numrows($subs);$i++){
				$name=mysql_result($subs, $i, 'username');
				$userid=mysql_result($subs, $i, 'UID');
				$s = new Subject($userid);
				$btid = $s->getBt_id($id);
				//$token = randomString(12);
				echo "<a href=\"./showuser.php?id=".encrypt($userid)."\">$name</a> <div class=\"btinput\" id=\"btiddiv\">
				<input type=\"text\" name=\"bt$userid\" id=\"btid\" value=\"$btid\" maxlength=\"12\" size=\"12\" class=\"btidinput\" /></div>";
			}//value=\"$token\"
			echo "<input type=\"hidden\" name=\"id\" value=\"$research_id\" />";
			echo "</form>";
			echo "<p><a class=\"button\" href=\"#\" onclick=\"this.blur();validateBtTest()\"><span>Set</span></a>";
			echo '<a class="button" href="displayResearch.php?id='.$research_id.'"><span>Back</span></a></p>';
		}else{
			echo "<b>There are no respondents in this research.<br /> Add respondents to research first.</b>";
			echo '<a class="button" href="displayResearch.php?id='.$research_id.'"><span>Back</span></a></p>';
		}
	  	echo "</div></div>";
	}else{
		echo "<h1>Research has ended! Cannot edit it anymore</h1>";
		echo '<a class="button" href="displayResearch.php?id='.$research_id.'"><span>Back</span></a></p>';
	}
}//end of secured content

//Generates the token of 12 character
function randomString($length){
    // Generate random 32 charecter string
    $string = md5(time().rand(0,10000));
    // Position Limiting
    $highest_startpoint = 32-$length;

    // Take a random starting point in the randomly
    // Generated String, not going any higher then $highest_startpoint
    $randomString = substr($string,rand(0,$highest_startpoint),$length);

    return $randomString;
}
?>