<?php
class UserSQLQueryer extends SQLQueryer{


	function addUser($username, $password, $researchOwner=false, $superAdmin=false){
		require("../common/auth_start.php");
		$a = new Auth("MDB2", $options, "loginFunction");
		$result1=$a->addUser($username, $password);
		if($result1==true){
			$query="SELECT `UID` FROM `tbl_auth` WHERE `username` = '$username' ORDER BY `UID` DESC LIMIT 1;";
				//Check the ID of the created user
				$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
				if(MDB2::isError($result)){
					return false;
				}else{
					//Adding the new user to the database.	
					$native_result = $result->getResource();
					$row=Mysql_Fetch_Row($native_result);
					$id = $row[0];
					if($researchOwner==true){ //Check if user is a research owner, if yes, add a right
						$result1=$this->addResearchOwnerRight($id);
					}
					if($superAdmin==true){
						$result2=$this->addSuperAdminRight($id);
					}
					if($result1==false||$result2=false){
						return false;
					}else{
						return $id;
					}
				}
		}
		
	}//end function
	//Carolir:
		//function addUserRight($research, $UID, $admin=false, $researcher=false, $subject=false, $btid)
	function addUserRight($research, $UID, $admin=false, $researcher=false, $subject=false , $token){ //Adds user right to database, based on research ID num and user ID
		$query=("INSERT INTO `tbl_user_rights` (`research_id` ,`UID` ,`subject`, `researcher` ,`admin`) VALUES ('$research', '$UID', '$subject', '$researcher', '$admin');");
		
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			var_dump($result);
			return false;
			
		}else{
			if($subject==true){
				/*
				 * 	Bluetooth id and active has to be set separetly
				 */
				//$query2=("INSERT INTO `tbl_subject` (`subject_id` ,`UID` ,`research_id`, `bt_id` ,`active`) VALUES ('NULL', '$UID', '$research', '0', '0');");
				$query2=("INSERT INTO `tbl_subject` (`subject_id` ,`UID` ,`research_id`, `bt_id` ,`active`) VALUES ('NULL', '$UID', '$research', '$token', '0');");																											
																															//second last param 	
				$result2 = $this->mdb2->query($query2) or die('An unknown error occurred while updating the data');
				if(MDB2::isError($result2)){
					return false;			
				}else{
					return true;
				}
			}else{
				return true;
			}
		}

	}
	
	function setAdmin($research, $UID){ //Adds user right to database, based on research ID num and user ID
		$query=("UPDATE `tbl_user_rights` SET `admin` = '1' WHERE `research_id`='$research' AND `UID` ='$UID' LIMIT 1 ;");
		
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;		
		}
	}
	
	function rmAdmin($research, $UID){ //Removes user right to database, based on research ID num and user ID
		$query=("UPDATE `tbl_user_rights` SET `admin` = '0' WHERE `research_id`='$research' AND `UID` ='$UID' LIMIT 1 ;");
		
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;		
		}
	}
	
	function setResearcher($research, $UID){ //Adds user right to database, based on research ID num and user ID
		$query=("UPDATE `tbl_user_rights` SET `researcher` = '1' WHERE `research_id`='$research' AND `UID` ='$UID' LIMIT 1 ;");		
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;	
		}
	}
	
	function rmResearcher($research, $UID){ //Adds user right to database, based on research ID num and user ID
		$query=("UPDATE `tbl_user_rights` SET `researcher` = '0' WHERE `research_id`='$research' AND `UID` ='$UID' LIMIT 1 ;");		
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;	
		}
	}
	
	function setSubject($research, $UID){ //Adds user right to database, based on research ID num and user ID
		$query=("UPDATE `tbl_user_rights` SET `subject` = '1' WHERE `research_id`='$research' AND `UID` ='$UID' LIMIT 1 ;");		
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$query=("SELECT `subject_id` FROM `tbl_subject` WHERE `UID` ='$UID' AND `research_id`='$research' LIMIT 1;");		
			$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($result)){
				return false;
			}else{
				$native_result = $result->getResource();
				if(mysql_numrows($native_result)>0){
					return true;
				}else{
					$query=("INSERT INTO `tbl_subject` (`subject_id` ,`UID` ,`research_id`, `bt_id` ,`active`) VALUES (NULL, '$UID', '$research', NULL, 0);");		
					$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
					if(MDB2::isError($result)){
						return false;
					}else{
						return true;	
					}	
				}
			}	
		}
	}
	
	function rmSubject($research, $UID){ //Adds user right to database, based on research ID num and user ID
		$query=("UPDATE `tbl_user_rights` SET `subject` = '0' WHERE `research_id`='$research' AND `UID` ='$UID' LIMIT 1 ;");		
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$query=("DELETE FROM `tbl_subject` WHERE `UID` = '$UID' AND `research_id`='$research' LIMIT 1;");		
			$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($result)){
				return false;
			}else{
				$query=("SELECT `subject`,`researcher`,`admin` FROM `tbl_user_rights` WHERE `UID` = '$UID' AND `research_id`='$research';");		
				$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
				if(MDB2::isError($result)){
					return false;
				}else{
					$native_result = $result->getResource();
					$subject = mysql_result($native_result,0,'subject');
					$res = mysql_result($native_result,0,'researcher');
					$admin = mysql_result($native_result,0,'admin');
					if($subject==0 && $res==0 && $admin==0){
						$query=("DELETE FROM `tbl_user_rights` WHERE `UID` = '$UID' AND `research_id`='$research';");		
						$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
						if(MDB2::isError($result)){
							return false;
						}else{
							$query=("DELETE FROM `tbl_subject` WHERE `UID` = '$UID' AND `research_id`='$research';");		
							$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
							if(MDB2::isError($result)){
								return false;
							}else{
								return true;	
							}		
						}	
					}	
				}	
			}			
		}
	}
	

	function getLocalUsers($research, $type="all"){
		switch($type){
			case "0":{
				$query=("SELECT a.`username`, a.`UID` FROM `tbl_auth` as a, `tbl_user_rights` as r WHERE a.UID = r.UID AND admin = 1 AND r.research_id=$research ORDER BY a.username;");
				break;
			}
			case "1":{
				$query=("SELECT a.`username`, a.`UID` FROM `tbl_auth` as a, `tbl_user_rights` as r WHERE a.UID = r.UID AND researcher = 1 AND r.research_id=$research ORDER BY a.username;");
				break;
			}	
			
			case "2":{
				$query=("SELECT a.`username`, a.`UID` FROM `tbl_auth` as a, `tbl_user_rights` as r WHERE a.UID = r.UID AND subject = 1 AND r.research_id=$research ORDER BY a.username;");
				break;	
			}
				
			case "all":{
				$query=("SELECT a.`username`, a.`UID` FROM `tbl_auth` as a, `tbl_user_rights` as r WHERE a.UID = r.UID AND r.research_id=$research ORDER BY a.username;");
				break;
			}
		}
		
	$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
	if(MDB2::isError($result)){
		//DEBUG: var_dump($result);
		return false;
	}else{
		$native_result = $result->getResource();
		return $native_result;						//Retuns a native Mysql Result of local users...
	}
}//end function
	function getInfo($id){
			$this->getLocalUsers($id);
		}

	function addSuperAdminRight($id){
		$query= "UPDATE `tbl_auth` SET `su_admin` = '1' WHERE `UID` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
	if(MDB2::isError($result)){
		return false;
	}else{
		return true;
	}	
	}//end function
	
	function rmSuperAdminRight($id){
		$query= "UPDATE `tbl_auth` SET `su_admin` = '0' WHERE `UID` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
	if(MDB2::isError($result)){
		return false;
	}else{
		return true;
	}	
	}//end function

	function addResearchOwnerRight($id){
		$query= "UPDATE `tbl_auth` SET `research_owner` = '1' WHERE `UID` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
	if(MDB2::isError($result)){
		return false;
	}else{
		return true;
	}	
	}//end function
	
	function rmResearchOwnerRight($id){
		$query= "UPDATE `tbl_auth` SET `research_owner` = '0' WHERE `UID` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
	if(MDB2::isError($result)){
		return false;
	}else{
		return true;
	}	
	}//end function
	
	function setSQLInsertQueryFunctionStub(){					//Documentation goes here
		$query= "";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
	if(MDB2::isError($result)){
		return false;
	}else{
		return true;
	}	
	}//end function
		
		
		
	
	function getSQLInsertQueryFunctionStub(){					//Documentation goes here
		$query= "";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//What does it return?.

		}
	}//End function	

		
	
	private function createAuthTable(){
		$query="CREATE TABLE `tbl_auth` (`UID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,`password` CHAR( 32 ) NOT NULL ,`su_admin` BINARY NOT NULL ,`username` VARCHAR( 64 ) NOT NULL, `research_owner` BOOL NOT NULL) ENGINE = MYISAM ;"; 
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
	}else{
		return true;
	}
	}//end Function
	
	private function createUserRightTable(){			//Function to create the tbl_user_rights
		$query="CREATE TABLE `tbl_user_rights` (`research_id` INT NOT NULL ,`UID` INT NOT NULL ,`subject` BOOL NOT NULL ,`researcher` BOOL NOT NULL ,`admin` BOOL NOT NULL) ENGINE = MYISAM CHARACTER SET latin1 COLLATE latin1_general_ci"; 
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
	}else{
		return true;
	}
	}//end Function

	
	
	function createTables2DB(){
		$temp=$this->createAuthTable();
		$temp2=$this->createUserRightTable();
		if($temp==false || $temp2==false ){
			return false;
		}else{
			return true;
		}
	}													//end function
	
	
	function getUser($id){
		$query= "SELECT  * FROM `tbl_auth` WHERE `UID` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			return $native_result;				//Returns the name of the user based on the ID given

		}
	} //end of getUser function
	
	
	
	function getName($id){					//<-- oma lis�ys Returns the name of the user based on the ID given
		$query= "SELECT  `username` FROM `tbl_auth` WHERE `UID` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the name of the user based on the ID given

		}
	}//End of getName function	
	
	
	function setName($id,$name){
		$query= "UPDATE `tbl_auth` SET `username` = '$name' WHERE `UID` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}
	}//end of function setName
	
	function isLocalAdmin($research, $uid){
		$query= "SELECT * FROM `tbl_user_rights` WHERE `research_id` = $research AND `UID` = $uid AND `admin` = 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$num=mysql_numrows($native_result);
			//var_dump($row);
			if($num!=0){
				return true;
			}else{
				return false;
			}			//Returns the name of the user based on the ID given

		}
	}//End of isLocalAdmin function
	
	function isLocalResearcher($research, $uid){
		$query= "SELECT * FROM `tbl_user_rights` WHERE `research_id` = $research AND `UID` = $uid AND `researcher` = 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$num=mysql_numrows($native_result);
			//var_dump($row);
			if($num!=0){
			return true;}else{
				return false;
			}			//Returns the name of the user based on the ID given

		}
	}//End of isLocalAdmin function

	function isLocalSubject($research, $uid){
		$query= "SELECT * FROM `tbl_user_rights` WHERE `research_id` = $research AND `UID` = $uid AND `subject` = 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$num=mysql_numrows($native_result);
			//var_dump($row);
			if($num!=0){
			return true;}else{
				return false;
			}			//Returns the name of the user based on the ID given

		}
	}//End of isLocalSubject function
	
	function hasRights($research, $uid){
		$query= "SELECT * FROM `tbl_user_rights` WHERE `research_id` = $research AND `UID` = $uid AND (`admin` = 1 OR `subject` = 1 OR `researcher` = 1);";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$num=mysql_numrows($native_result);
			//var_dump($row);
			if($num!=0){
			return true;}else{
				return false;
			}			//Returns true or false based on if user has rights

		}
	}//End of isLocalAdmin function
	
	static function getAllUsers(){
	require("../settings/dbsettings.php");
	$query="SELECT `UID`,`username` FROM `tbl_auth` ORDER BY `username`";
		$result = $mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$result_assoc=Mysql_Fetch_Assoc($native_result);
			if(count($result_assoc)>0){
				return $native_result;
			}else{
				return false;
			}
		}
	}
	
	
	function checkUsername($username){
		$query= "SELECT * FROM `tbl_auth` WHERE `username` = '$username';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$num=mysql_numrows($native_result);
			if($num==0){
				return true;
			}else{
				return false;
			}			//Returns the num of rows queried,if 0 username is available

		}
	}//end of checkUsername
	
	
	
	function getRoles($id){
		$query="SELECT tbl_research.research_name,tbl_user_rights.research_id,tbl_user_rights.subject,tbl_user_rights.researcher,tbl_user_rights.admin FROM tbl_research,tbl_user_rights,tbl_auth WHERE tbl_auth.UID='$id' AND tbl_user_rights.UID = tbl_auth.UID AND tbl_research.research_id = tbl_user_rights.research_id;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$values = array();
			for($i=0;$i<mysql_numrows($native_result);$i++){
				$values[$i] = array(
				'research' => mysql_result($native_result,$i,"research_id"),
				'researchname' => mysql_result($native_result,$i,"research_name"),
				'subject' => mysql_result($native_result,$i,"subject"),
				'researcher' => mysql_result($native_result,$i,"researcher"),
				'admin' => mysql_result($native_result,$i,"admin"),
				);
			}
			return $values;
		}
	}//end of function
	
	function isSuperadmin($id){
	$query="SELECT `su_admin` FROM `tbl_auth` WHERE `UID`='$id'";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			if($row[0]==1){
				return true;
			}else{
				return false;
			}
		}
	}
	
	function isResearchowner($id){
	$query="SELECT `research_owner` FROM `tbl_auth` WHERE `UID`='$id'";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			if($row[0]==1){
				return true;
			}else{
				return false;
			}
		}
	}
	
	function setPwd($id,$pwd){
		$query= "UPDATE `tbl_auth` SET `password` = '$pwd' WHERE `UID` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError";
		}else{
			return true;
		}
	}//end of function setName
	
	function getPwd($id){
		$query= "SELECT `password` FROM `tbl_auth` WHERE `UID` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];
		}
	}//end of function setName
	
	function rmROwnerRight($id){
		$query= "UPDATE `tbl_auth` SET `research_owner` = '0' WHERE `UID` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError";
		}else{
			return true;
		}
	}
	
	function addROwnerRight($id){
		$query= "UPDATE `tbl_auth` SET `research_owner` = '1' WHERE `UID` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError";
		}else{
			return true;
		}
	}
	
	function rmSuperARight($id){
		$query= "UPDATE `tbl_auth` SET `su_admin` = '0' WHERE `UID` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError";
		}else{
			return true;
		}
	}
	
	function addSuperARight($id){
		$query= "UPDATE `tbl_auth` SET `su_admin` = '1' WHERE `UID` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError";
		}else{
			return true;
		}
	}
	
	function hasRightToLoginIn($uid){
		$query= "SELECT * FROM `tbl_user_rights` WHERE `UID` = $uid AND (`admin` = 1 OR `researcher` = 1);";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$num=mysql_numrows($native_result);
			if($num!=0){
			return true;}else{
				return false;
			}			//Returns true or false based on if user has rights

		}
	}//End of isLocalAdmin function
	
	function rmUser($id){
		$query= "DELETE FROM `tbl_user_rights` WHERE `UID` = $id";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
		if(MDB2::isError($result)){							//check weather query succeeded
			return false;
		}else{
			$query= "DELETE FROM `tbl_auth` WHERE `UID` = $id LIMIT 1";
			$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
			if(MDB2::isError($result)){							//check weather query succeeded
				return false;
			}else{
				$query= "DELETE FROM `tbl_subject` WHERE `UID` = $id";
				$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
				if(MDB2::isError($result)){							//check weather query succeeded
					return false;
				}else{
					return true;
				}
			}
		}
	}//end of function
	
	function localPrivar($rid,$uid){
		$query= "SELECT `tbl_privar`.`subject_id`,`tbl_privar`.`privateVar`,`tbl_privar`.`number`,`tbl_privar`.`var_id` FROM `tbl_privar`,`tbl_subject` WHERE `tbl_subject`.`UID` = '$uid' AND `tbl_subject`.`research_id` = '$rid' AND `tbl_subject`.`subject_id` = `tbl_privar`.`subject_id` ORDER BY `number` ASC;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$privar = array();
			$num=mysql_numrows($native_result);
				for($i=0;$i<$num;$i++){
					$privar[$i]=array(
						'subject_id' => mysql_result($native_result,$i,"subject_id"),
						'privar' => mysql_result($native_result,$i,"privateVar"),
						'number' => mysql_result($native_result,$i,"number"),
						'var_id' => mysql_result($native_result,$i,"var_id"),
						);
					}
				return $privar;					//Returns the subjects private variables in an array
		}
	}
	
	function createPrivar($uid,$rid,$privar,$number){
		$query= "SELECT `subject_id` FROM `tbl_subject` WHERE `UID` = '$uid' AND `research_id` = '$rid' LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			$query= "INSERT INTO `tbl_privar` (`var_id` ,`privateVar` ,`subject_id`, `number`) VALUES ('NULL','$privar', '$row[0]', '$number');";
			$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "InsertError";
			}else{
				return true;
			}
		}
	}//end of function
	
	function setPrivar($uid,$rid,$privar,$number){
		$query= "SELECT `subject_id` FROM `tbl_subject` WHERE `UID` = '$uid' AND `research_id` = '$rid' LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			$query= "UPDATE `tbl_privar` SET `privateVar` = '$privar' WHERE `number` =$number AND `subject_id`='$row[0]' LIMIT 1 ";
			$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "InsertError";
			}else{
				return true;
			}
		}
	}//end of function
	
	function unlock($uid){
		$query="SELECT `research_id` FROM `tbl_research`  WHERE `locked`=$uid;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$num=mysql_numrows($native_result);
			if($num>0){
				$row=Mysql_Fetch_Row($native_result);
				for($i=0;$i<$num;$i++){
					$r = new Research($row[0]);
					$ok = $r->unLock($uid);
					if($ok==false){
						return false;
						break;
					}
				}
				}
			}
		$query="SELECT `query_id` FROM `tbl_query`  WHERE `locked`=$uid;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$num=mysql_numrows($native_result);
			if($num>0){
				$row=Mysql_Fetch_Row($native_result);
				for($i=0;$i<$num;$i++){
					$q = new Query($row[0]);
					$ok = $q->unLock($uid);
					if($ok==false){
						return false;
						break;
					}
				}
			}						
		}
		return true;
	}
	
	
}														//end class
?>