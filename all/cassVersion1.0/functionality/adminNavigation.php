<?php
if($a->getAuthData('su_admin')==1){
	echo "<div id=\"descriptiondiv\">";
	echo "<div class=\"descrheader\"><div class=\"headertext\"><h1>Administrator page</h1><hr /></div></div>";
	echo "<br /><br />";
	if(isset($id)){
		if($action=="rmu"){
			$user = new User($id);
			if($user->rmUser()){
				echo "<h2>User removed successfully!</h2>";
			}
		}
	}
}
?>