<?php
$num = $_GET['n'];
$submit = $_POST['submit_luo'];
//$research_id = $_GET['id'];

if($r->users->isLocalAdmin($UID)){
	$locked = $r->isLocked();
	if($locked!="Ended" && $locked!="freezed"){
	if(($num==null||!is_numeric($num)||$num==0)&&!isset($_POST['usn0'])){
		echo "<div id=\"addsubsdiv\">";
		echo "<div class=\"descrheader\"><div class=\"headertext\"><div id=\"help\" onclick=\"openhelp('addsub');\">?</div>
			<h1>Add respondents to research</h1><hr /></div></div>";
		echo("<div class=\"addsubcont\"><br />Research: ".$r->getName()."");
		echo "<br /><br />";
		/*echo("  <form id=\"addsubform\" action=\"./addnsubjects.php\" method=\"get\" enctype=\"multipart/form-data\">
	  	Choose amount of subjects:
	 	<input type=\"text\" name=\"n\" value=\"0\" size=\"4\" maxlength=\"2\"/>
	  	<input type=\"hidden\" name=\"id\" value=\"$research_id\" />");
	  //	echo "<input type=\"submit\" name=\"submit1\" value=\"Create\"/>";
	  	echo "<div class=\"addsubbutton\"><a class=\"button\" href=\"#\" onclick=\"this.blur();sendform('addsubform')\"><span>Create</span></a></div>";
	  	echo "</form>";*/
		echo subnumform($research_id);
		
	  	echo "</div></div>";
	}elseif(!isset($_POST['usn0'])){
		$research_id = $_GET['id'];
		echo "<div id=\"addsubsdiv\">";
		echo "<div class=\"descrheader\"><div class=\"headertext\"><div id=\"help\" onclick=\"openhelp('addsub');\">?</div><h1>Add respondents to research</h1><hr /></div></div>";
		echo "<div class=\"addsubcont\"><a href=\"../content/addnsubjects.php?id=$research_id\"><- Back</a>";
		echo("<table>");
		echo("<form id=\"addsubform2\" action=\"./addnsubjects.php\" method=\"post\" enctype=\"multipart/form-data\">");
		echo("<input type=\"hidden\" name=\"n\" value=\"$num\">");	
		echo("<tr><th>Username</th><th>Password</th>");
		
		for($i = 0; $i<$num; $i++){
			$randomPwd= randomString(8);
			$randomUsn= randomString(8);
			/*
			 * Generates 12 charactered token
			 * starts with t+11 string of random numbers
			 */
			$token= randomToken(11);
			echo("<tr>");
			echo("<td>Username:<input type=\"text\" name=\"usn$i\" value=\"$randomUsn\" size=\"10\" maxlength=\"40\"/></td>");
			echo("<td>Password:<input type=\"text\" name=\"pwd$i\" value=\"$randomPwd\" size=\"10\" maxlength=\"40\"/></td>");
			echo("<td>Token:<input type=\"text\" readonly=\"readonly\" name=\"tok$i\" value=\"$token\" size=\"10\" maxlength=\"40\"/></td>");//token purpose
			echo("<input type=\"hidden\" name=\"id\" value=\"$research_id\" />");
			echo("</tr>");
		}
		//echo("<tr><td><input type=\"submit\" name=\"submit_luo\" value=\"Create\"/></td></tr>");
		echo("<tr><td><br /><a class=\"button\" href=\"#\" onclick=\"this.blur();sendform('addsubform2')\"><span>Create</span></a></td></tr>");
		echo("</form>");
		echo("</table>");
		echo "</div></div>";
	}else{
		//$research_id = $_POST['id'];
		$sqlcon= new UserSQLQueryer();
		if($sqlcon->isLocalAdmin($id, $a->getAuthData('uid'))){
			$num = $_POST['n'];
			echo "<div id=\"addsubsdiv\">";
			echo "<div class=\"descrheader\"><div class=\"headertext\"><h1>Add respondents to research</h1><hr /></div></div>";
			echo "<div class=\"addsubcont\">";
			echo("<p><b>Kindly note:</b> Please print the username/password list, or save it, if you wish to distribute it to the respondents ");
			echo("after you leave this page, there is no way to retrieve them.</p>");
			echo("<table><th><h2>User ID</h2></th><th><h2>Username</h2></th><th><h2>Password</h2></th></tr>");
			$doc = "<h1>Respondents</h1><h2>Research: ".$r->getName()."</h2>";
			//$doc .= "<table><th>User ID</th><th>Username</th><th>Password</th></tr>";
			$doc .= "<table><th>User ID</th><th>Username</th><th>Password</th><th>Token</th></tr>";//token purpose
		
			for($i = 0; $i<$num; $i++){						
				$s_username = strip_tags($_POST["usn$i"]);
				$pwd1 =$_POST["pwd$i"];
				$tok_en = $_POST["tok$i"];//token purpose
				if(!empty($s_username) && !empty($pwd1)){
					if($sqlcon->checkUsername($s_username)){
						$uid=$sqlcon->addUser($s_username, $pwd1);	
						//adding subject (because $subject=true) rights on the research
						//$sqlcon->addUserRight($id, $uid, false, false, true);
							//function of addUserRight receives now one param more: $btid
							$sqlcon->addUserRight($id, $uid, false, false, true, $tok_en);	//token purpose	
					}
					//echo("<tr><td> UID: $uid</td><td> Username: $s_username</td><td> Password: $pwd1</td></tr>");
					//$doc .="<tr><td> UID: $uid</td><td> Username: $s_username</td><td> Password: $pwd1</td></tr>"; //This is posting for the file to download later.
					echo("<tr><td> UID: $uid</td><td> Username: $s_username</td><td> Password: $pwd1</td><td>Token: $tok_en</td></tr>");//token purpose
					$doc .="<tr><td> UID: $uid</td><td> Username: $s_username</td><td> Password: $pwd1</td><td>Token: $tok_en</td></tr>";//token purpose
				}else{
					echo("<tr><td>". $i+1 .". Respondent had invalid username or password</td></tr>");
					$doc .="<tr><td>". $i+1 .". Respondent had invalid username or password</td></tr>";				
				}
			}//end for
			echo("</table>");
			$doc .="</table>";
			echo "<br /><br /><form id=\"printsubsform\" action=\"../functionality/getAnswers.php\" method=\"post\" enctype=\"multipart/form-data\">";
			echo("<input type=\"hidden\" name=\"subs\" value=\"$doc\" />");
			echo("<input type=\"hidden\" name=\"rid\" value=\"".encrypt($id)."\" />");
			//echo("<input type=\"submit\" name=\"printSub\" value=\"Print\" />");
			echo "<a class=\"button\" href=\"#\" onclick=\"this.blur();sendform('printsubsform')\"><span>Download</span></a>";
			echo "</form>";
			echo "</div></div>";
			
		}else{
			echo("You have no right to add respondents to this research!");
		}
	}
	}else{
		echo "<h1>Research has ended! Cannot edit it anymore</h1>";
	}
}//end of secured content

function randomString($length){
    // Generate random 32 charecter string
    $string = md5(time().rand(0,10000));
    // Position Limiting
    $highest_startpoint = 32-$length;

    // Take a random starting point in the randomly
    // Generated String, not going any higher then $highest_startpoint
    $randomString .= substr($string,rand(0,$highest_startpoint),$length);
	
    return $randomString;
}

function randomToken($length){
    // Generate random 32 charecter string
    $string = md5(time().rand(0,10000));
    // Position Limiting
    $highest_startpoint = 32-$length;

    // Take a random starting point in the randomly
    $randomString = "t";
    // Generated String, not going any higher then $highest_startpoint
    $randomString .= substr($string,rand(0,$highest_startpoint),$length);
	
    return $randomString;
}

function subnumform($rid){
	return ("  <form id=\"addsubform\" action=\"./addnsubjects.php\" method=\"get\" enctype=\"multipart/form-data\">
	  	Choose amount of respondents:
	 	<input type=\"text\" name=\"n\" value=\"0\" size=\"4\" maxlength=\"2\"/>
	  	<input type=\"hidden\" name=\"id\" value=\"$rid\" />
	  	 <div class=\"addsubbutton\"><a class=\"button\" href=\"#\" onclick=\"this.blur();sendform('addsubform')\"><span>Create</span></a></div>
	  	 </form>");
}
?>
