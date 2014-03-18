<?php
require_once("../common/auth_loginf.php"); //Login form generation function include
require_once("../common/auth_start.php");
require_once("../common/includes.php");
if(isset($_GET['submit'])){
	//echo 'get is set';
	$qid = decrypt($_GET['qid']);
	//echo $qid;
	$query = new Query($qid);
	$visualize = $_GET["visualize"];
	//echo $visualize;
	if(isset($visualize) && $visualize == "true"){
		$query->updateQueryVisualization("1");
		//echo added;
	}else{
	//	echo "comes to dont visualize";
		$query->updateQueryVisualization("0");
	}
	$qid = encrypt($qid);
	header("Location: ../content/displayQuery.php?id=$qid");
	
}
?>