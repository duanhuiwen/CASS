<?php
if($a->getAuthData('su_admin')==1){ //Check user level
$usn=$a->getUsername();
$id = $_GET['id'];
	include('adminNavigation.php');
		require_once("../functionality/listUsers.php");
		echo "<div class=\"description\">";
		SuperAdminsListAllUsers();	
		echo "</div></div>";
}
?>