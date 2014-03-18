<?php

if($r->users->isLocalAdmin($UID)){ //Check user level
	$locked = $r->isLocked();
	if($locked!="Ended" && $locked!="freezed"){
	if(isset($_POST['privar1'])){
		$rid = decrypt($_POST['rid']);
		$id =decrypt($_POST["id"]);
		$num = $_POST['n'];
		echo "<br />";
		$research = new Research($rid);
		$user = new User($id);
		$username = $user->getName();				
		$researchname = $research->getName();
		$privars = $research->users->localPrivar($id);
		echo "<div id=\"navpath\"><a href=\"../content/index.php\"> Home </a> >> <a href=\"../content/displayResearch.php?id=$research_id\"> $researchname </a> >> <a href=\"../content/showuser.php?id=".encrypt($id)."\"> $username </a> >> <a href=\"#\"> Edit private variables </a></div>";
		echo "<div id=\"descriptiondiv\">";
		echo "<div class=\"descrheader\"><div class=\"headertext\"><h1>Private variables</h1><hr /></div></div>";
		echo "<div class=\"description\">";
		echo("<b>User:</b> <a href=\"../content/showuser.php?id=".$_POST["id"]."\">$username</a> <br /><b>Research:</b> $researchname<br /><br /><b>Updated private variables:</b><br />");
		$sql = new SubjectSQLQueryer();
		for($i=0;$i<count($privars);$i++){
			$sql->rmPrivar($privars[$i]['var_id']);
		}
		for($i=0;$i<$num;$i++){
			$j=$i+1;
			$privar = strip_tags($_POST["privar$j"]);
			if(!empty($privar)){
				if($research->users->createLocalPrivar($id,$privar)){
					echo("<b> $j:</b> $privar<br />");
				}
			}
		}
		/*if(count($privars)<=$num){
			for($i = 1; $i<=$num; $i++){			
				$privar = strip_tags($_POST["privar$i"]);
				$j = $i-1;
				if(!empty($privars[$j]['privar'])){
					if($privars[$j]['privar']!=$privar){				
						$research->users->setLocalPrivar($id,$privar,$privars[$j]['number']);
						echo("<b> $i:</b> $privar<br />");
					}
				}else{
					$research->users->createLocalPrivar($id,$privar);
					echo("<b> $i:</b> $privar<br />");
				}
			}//end for
		}else{
			$sql = new SubjectSQLQueryer();
			for($i=0;$i<count($privars);$i++){
				$j = $i+1;
				if($privars[$i]['privar']!=$_POST['privar'.$j]){
					$sql->rmPrivar($privars[$i]['var_id']);
				}
			}
			for($k=1;$k<$num;$k++){
				$l = $k-1;
				if($privars[$l]['privar']!=$_POST['privar'.$k]){
					$research->users->createLocalPrivar($id,$_POST['privar'.$k]);
				}
			}
		}
			echo("</table>");*/
			echo "</div></div>";
	}else{
		$privars = $r->users->localPrivar($uid);
		if(!empty($privars)){
			$num = count($privars);
			echo "<div id=\"navpath\"><a href=\"../content/index.php\"> Home </a> >> <a href=\"../content/displayResearch.php?id=$research_id\"> $rname </a> >> <a href=\"../content/showuser.php?id=".encrypt($uid)."\"> $username </a> >> <a href=\"#\"> Edit private variables </a></div>";
			echo "<div id=\"descriptiondiv\">";
			echo "<div class=\"descrheader\"><div class=\"headertext\"><h1>Add Private variables</h1><hr /></div></div>";
			//echo "<a class=\"backlink\" href=\"../content/showuser.php?id=".$_GET["id"]."\"><- Back</a>";
			echo "<div class=\"description\">";
			echo "<div id=\"invalidform\"><div class=\"errortxt\"></div></div>";
			echo "<h3>Research: $rname</h3>";
			echo "<h2>User: $username</h2>";	
			echo "<form id=\"editprivarform\" action=\"./editPrivar.php\" method=\"post\" enctype=\"multipart/form-data\">";
		  	echo "Choose amount of private variables:";
		 	echo "<input type=\"text\" id=\"n\" name=\"n\" value=\"$num\" size=\"2\" maxlength=\"1\" onKeyUp=\"createPrivars(this.value)\" /><br />";
		 	echo "<div id=\"newPrivars\" name=\"newPrivars\">";
		 	for($i=0;$i<$num;$i++){
		 		$row = $i+1; 	
		 		echo "<div id=\"priv$row\" name=\"priv\">";
		 		echo "$row. <input type=\"text\" name=\"privar$row\" id=\"privar$row\" value=\"".$privars[$i]['privar']."\" /><br />";
		 		echo "</div>";
		 	}
		 	echo "</div>";
			echo "<input type=\"hidden\" name=\"rid\" value=\"$research_id\" />";
		 	echo "<input type=\"hidden\" name=\"id\" value=\"".encrypt($uid)."\" />";
		  //	echo "<br /><input type=\"submit\" name=\"submit1\" value=\"Update\"/>"; 
		  	echo "<br /><a class=\"button\" href=\"#\" onclick=\"this.blur();validateAddprivar('editprivarform')\"><span>Update</span></a>";
		  	echo "</form>";
		  	echo "</div></div>";
		}
	}
	}else{
		echo "<h1>Research has ended! Cannot edit it anymore</h1>";
	}
}else{
	echo "Access denied,you are not allowed to browse this data.";
}

?>