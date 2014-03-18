<?php
if($r->users->isLocalAdmin($UID)){
	$id = decrypt($_POST['id']);
	$post = $_POST;
	echo "<div id=\"addsubsdiv\">";
	echo "<div class=\"descrheader\"><div class=\"headertext\"><h1>Set bluetooth id to respondents</h1><hr /></div></div>";
	echo("<div class=\"addsubcont\">Research: ".$r->getName()."");
	$subs = $r->users->getSubjects();
	echo "<h2>Bluetooth ID's set to:</h2>";
	echo "<table cellspacing=\"10\"><tr><th>Name</th><th>Bluetooth ID</th></tr>";
	for($i=0;$i<mysql_numrows($subs);$i++){
		$name=mysql_result($subs, $i, 'username');
		$userid=mysql_result($subs, $i, 'UID');
		$s = new Subject($userid);
		$btid = $s->getBt_id($id);
		$bt = strtoupper($_POST['bt'.$userid]);
		if(isset($bt)){
			if(empty($bt)){
				$s->setBt_id($bt,$id);
				echo "<tr><td>$name</td><td>$bt</td></tr>";
			}else{
				if(strlen($bt)==12 && preg_match("([0-9A-Za-z]{12})",$bt)){
					if($s->checkBtId($bt,$id)){
						$s->setBt_id($bt,$id);
						echo "<tr><td>$name</td><td>$bt</td></tr>";
					}
				}
			}
		}
	}
	echo "</table></div></div>";
}
?>