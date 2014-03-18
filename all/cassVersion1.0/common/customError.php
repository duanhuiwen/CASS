<?php
function errorHandler($code,$msg,$file,$line,$UID=0){
	include("../common/includes.php");
	//Login form generation function include
	require_once("../common/auth_loginf.php"); 
	require_once("../common/auth_start.php");
	//bringing in the top part of the layout, if the login function didn't already do that
	require_once("../UI/layout/top.php");
	
	echo "Unfortunately an error has occurred!";
	include("../UI/layout/bottom.php");
	//Log errors
	$UID = $UID['HTTP_SESSION_VARS']['_authsession']['data']['uid'];
	$eventlogger = new EventLogger($UID);
	$descr = addslashes("$code Error in file $file on line $line.Error message: $msg");
	$eventlogger->createLogItem($descr);
}

?>