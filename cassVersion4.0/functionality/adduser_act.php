<?php
if($a->getAuthData('research_owner')==1||$a->getAuthData('su_admin')==1){  
	require_once "Auth.php";
	$username=strip_tags($_POST['username']);
	$pwd1=$_POST['password'];
	$pwd2=$_POST['password2'];
	$sadmin =strip_tags($_POST['superadmin']);
	$rowner =strip_tags($_POST['researchowner']);
	$sqlcon= new UserSQLQueryer();
	
	if(empty($pwd1)){
		echo("You must specify a password!<br />");
		$error = true;
	}
	if(empty($username)){
		echo("You must specify a username!<br />");
		$error = true;
	}elseif($pwd1!=$pwd2) {
		echo("Passwords must match!<br />");
		$error = true;
	}else{
		if($sqlcon->checkUsername($username)){
			$id = $sqlcon->addUser($username, $pwd1, $rowner, $sadmin);
			$id = encrypt($id);
			if(!headers_sent()){
        			header('Location:../content/showuser.php?id='.$id.'');
    			}else{
       				echo '<script type="text/javascript">';
				    echo 'window.location.href="../content/showuser.php?id='.$id.'";';
				    echo '</script>';
				    echo '<noscript>';
				    echo '<meta http-equiv="refresh" content="0;url=../content/showuser.php?id='.$id.'" />';
				    echo '</noscript>';
   	 			}
		}else{
			echo("Username is taken.Please,specify a new username!<br />");
			$error = true;
		}
	}
	//if an error,include the add user form again
	if(!empty($error)){
		require_once("../functionality/adduser_frm.php");
	}
}

?>