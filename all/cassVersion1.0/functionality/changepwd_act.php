<?php
if($a->getAuthData('su_admin')==0 || $a->getAuthData('research_owner')==0 || $u->hasRightToLoginIn()){
	require_once "Auth.php";
	$username=strip_tags($_POST['username']);
	$pwdold=$_POST['oldpassword'];
	$pwd1=$_POST['password'];
	$pwd2=$_POST['password2'];
	$sadmin =$_POST['superadmin'];
	$rowner =$_POST['researchowner'];
	$sqlcon= new UserSQLQueryer();
	if(isset($_POST['uid'])){
		$UID = $_POST['uid'];
		$user = new User($_POST['uid']);
		$usn = $user->getName();
	}else{
		$user = new User($UID);
		$usn=$a->getUsername();
		$pwdold = md5($pwdold);
	}
	if(isset($pwdold)){
		if(!isset($pwd1) || $pwd1==null || $pwd2==null){
			echo("You must specify a password!<br />");
			$error = true;
		}
		if(!isset($username)){
			echo("You must specify a username!<br />");
			$error = true;
		}elseif($pwd1!=$pwd2) {
			echo("Passwords must match!<br />");
			$error = true;
		}else{
			if($usn == $username){
				if($user->changePwd($pwdold,md5($pwd1))){
					echo("<h2>User update succesful</h2>");
					echo "<a href=\"../content/showuser.php?id=".encrypt($UID)."\"> >> Show user $username</a>";
				}else{
					echo "Error!";
					$error = true;
				}
			}else{
				if($sqlcon->checkUsername($username)){			
					if($user->setName($username) && $user->changePwd($pwdold,md5($pwd1))){					
						echo("<h2>User update succesful</h2>");
						@$a->setAuth($username);			
						echo "<a href=\"../content/showuser.php?id=".encrypt($UID)."\"> >> Show user $username</a>";		
					}else{
						echo "Error!";
						$error = true;
					}
				}else{
					echo("Username is taken.Please,specify a new username!<br />");
					$error = true;
				}
			}
		}
		
	}else{
		echo "Old password must be set!<br />";
		$error = true;
	}
	
	//if an error,include the change username form
	if(!empty($error)){
		require_once("changepwd_frm.php");
	}
}else{
	echo "access denied!";

}
?>