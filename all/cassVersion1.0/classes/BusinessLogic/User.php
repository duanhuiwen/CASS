<?php
class User extends DatabaseWriteable{
	private $username;
	private $id;
	private $superadmin;
	private $researchowner;
	
	
	function __construct($id=0){
		parent::__construct();
		$this->id=$id;
		$this->dbq= new UserSQLQueryer();
		$this->subjects = new Subject($this->id);
		if($id!=0){
			$this->dbGet($id);
		}
	}
	
	function dbGet($id){
		$temp=$this->dbq->getUser($id);
		$num=mysql_numrows($temp);
		if($num==1){
			$this->id=mysql_result($temp,0,"UID");
			$this->name=mysql_result($temp,0,"username");
			$this->superadmin = $this->dbq->isSuperadmin($this->id);
			$this->researchowner = $this->dbq->isResearchowner($this->id);
			$this->inDB=true;
		return $this->id;
		}else return false;
		
	}
	
	
	function getName(){
		if($this->inDB==false||$this->username==null){
			$this->username=$this->dbq->getName($this->id);
		}
		return $this->username;
	}
	
	function setName($name){
		if($this->dbq->setName($this->id,$name)){
			return true;
		}else{
			return false;
		}
	}
	
	function getID(){
		return $this->id;
	}
	
	static function addUser($username, $password, $researchOwner=false, $superAdmin=false){
		$dbq->addUser($username, $password, $researchOwner, $superAdmin);//Use dbq object to add user
	}
	
	
	function checkUsername($username){
		if($this->dbq->checkUsername($username)){
			return true;
		}else{
			return false;
		}
	}
	
	function getRoles(){ //Function to get users roles
		$temp = $this->dbq->getRoles($this->id);
		return $temp;
	}//end of function getRoles
	
	function isSuperadmin(){
		if($this->inDB==false||$this->superadmin==null){
			$this->superadmin=$this->dbq->isSuperadmin($this->id);
		}
		return $this->superadmin;
	}
	
	function isResearchowner(){
		if($this->inDB==false||$this->researchowner==null){
			$this->researchowner=$this->dbq->isResearchowner($this->id);
		}
		return $this->researchowner;
	}
	
	function setPwd($pwd){
		if($this->dbq->setPwd($this->id,$pwd)){
			return true;
		}else{
			return false;
		}
	}
	
	function getPwd(){
		if($this->inDB == true){
			return $this->dbq->getPwd($this->id);
		}else{
			return false;
		}
	}
	
	function changePwd($oldpwd,$newpwd){ //Function to change pwd //REMIND AUTH CAN DO THIS TOO!
		if($this->getPwd()==$oldpwd && isset($newpwd)){
			if($this->getPwd()==$newpwd){
				return true;
			}else{
				if($this->setPwd($newpwd)){
					return true;
				}else{
					return false;
				}
			}			
		}else{
			return false;
		}
	} // end of function changePwd
	
	function rmSuperARight(){
		if($this->isSuperadmin()){
			if($this->dbq->rmSuperARight($this->id)){
				$this->superadmin = false;
				return true;
			}else{
				return false;
			}
		}
	}
	
	function addSuperARight(){
		if($this->isSuperadmin()==false){
			if($this->dbq->addSuperARight($this->id)){
				$this->superadmin = true;
				return true;
			}else{
				return false;
			}
		}
	}
	
	function rmROwnerRight(){
		if($this->isResearchowner()){
			if($this->dbq->rmROwnerRight($this->id)){
				$this->researchowner = false;
				return true;
			}else{
				return false;
			}
		}
	}
	
	function addROwnerRight(){
		if($this->isResearchowner()==false){
			if($this->dbq->addROwnerRight($this->id)){
				$this->researchowner = true;
				return true;
			}else{
				return false;
			}
		}
	}
	
	function hasRightToLoginIn(){ //function that checks that user is at mininum researcher
		if($this->isSuperadmin() || $this->isResearchowner()){
			return true;
		}else{
			if($this->dbq->hasRightToLoginIn($this->id)){
				return true;
			}else{
				return false;
			}
		}
	
	}
	
	function rmUser(){
		if($this->dbq->rmUser($this->id)){
			return true;
		}else{
			return false;
		}
	}
	
	function unlock(){
		if($this->dbq->unlock($this->id)){
			return true;
		}else{
			return false;
		}
		
	}
	
}// end of class
?>