<?php
if($r->users->isLocalAdmin($UID)){ //Check user level
	$locked = $r->isLocked();
	if($locked!="Ended" && $locked!="freezed"){
	$num = $_GET['n'];
	$submit = $_POST['submit_luo'];
	//Actual information in center
	echo "<div id=\"navpath\"><a href=\"../content/index.php\"> Home </a> >> <a href=\"../content/displayResearch.php?id=$research_id\"> $rname </a> >> <a href=\"../content/showuser.php?id=".encrypt($uid)."\"> $username </a> >> <a href=\"#\"> Add private variables </a></div>";
	echo "<div id=\"descriptiondiv\">";
	echo "<div class=\"descrheader\"><div class=\"headertext\"><h1>Add Private variables</h1><hr /></div></div>";
				//echo "<a class=\"backlink\" href=\"../content/showuser.php?id=".$_GET["id"]."\"><- Back</a>";
	echo "<div class=\"description\">";
	echo "<h3>Research: $rname</h3>";
	echo "<h2>User: $username</h2>";
	if(($num==null||!is_numeric($num)||$num==0)&&!isset($_POST['privar1'])){	
		echo("  <form id=\"addprivarform\" action=\"./addPrivar.php\" method=\"get\" enctype=\"multipart/form-data\">
	  		Choose amount of private variables:
	 	 	<input type=\"text\" name=\"n\" value=\"0\" size=\"4\" maxlength=\"2\"/>
	 	 	<input type=\"hidden\" name=\"rid\" value=\"$research_id\" />
	 	 	<input type=\"hidden\" name=\"id\" value=\"".encrypt($uid)."\" />");
	 	 	echo "<br />";
	 	 	echo "<br />";
	  //	echo "<input type=\"submit\" name=\"submit1\" value=\"Create\"/>";
	  echo "<div class=\"addprivcreate\"><a class=\"button\" href=\"#\" onclick=\"this.blur();sendform('addprivarform')\"><span>Create</span></a></div>";
	  		echo "</form>";
				
	}elseif(!isset($_POST['privar1'])){
		$research_id = decrypt($_GET['rid']);
		$uid = decrypt($_GET['id']);
		echo("<table>");
		echo("<form id=\"addprivarform2\" action=\"./addPrivar.php\" method=\"post\" enctype=\"multipart/form-data\">");
		echo("<input type=\"hidden\" id=\"n\" name=\"n\" value=\"$num\">");	
		echo("<tr><th>Private variable</th>");
		
		for($i = 1; $i<=$num; $i++){
			echo("<tr>");
			echo("<td>$i. <input type=\"text\" id=\"privar$i\" name=\"privar$i\" value=\"\" size=\"10\" maxlength=\"40\"/></td>");
			echo("</tr>");
		}
		echo("<input type=\"hidden\" name=\"rid\" value=\"".encrypt($research_id)."\" />");
		echo("<input type=\"hidden\" name=\"id\" value=\"".encrypt($uid)."\" />");
		//echo("<tr><td><input type=\"submit\" name=\"submit_luo\" value=\"Create\"/></td></tr>");
		echo("<tr><td><a class=\"button\" href=\"#\" onclick=\"this.blur();validateAddprivar('addprivarform2')\"><span>Create</span></a></td></tr>");
		echo("</form>");
		echo("</table>");
	}else{
		$sqlcon= new UserSQLQueryer();
		$rid = decrypt($_POST['rid']);
		$id =decrypt($_POST["id"]);
		$num = $_POST['n'];
		if($sqlcon->isLocalAdmin($rid, $a->getAuthData('uid'))){	
			$subject = new Subject($id);
			$user = new User($id);
			$username = $user->getName();
			$research = new Research($rid);
			$researchname = $research->getName();	
			echo("<b>User:</b><a href=\"../content/showuser.php?id=".$_POST["id"]."\">$username</a> <br /><b>Research: </b>$researchname<br /><br /><b>Private variables: </b><br />");
			for($i = 1; $i<=$num; $i++){			
				$privar = $_POST["privar$i"];
				$subject->setPrivar($privar);
				echo("<b> $i:</b> $privar<br />");
			}//end for
			
			echo("</table>");
		}else{
			echo("You have no right to add private variables to this research!");
		}
	}
	 echo "</div></div>";
	}else{
		echo "<h1>Research has ended! Cannot edit it anymore</h1>";
	}
}

?>