<?php
class LocalUserHandler{
	private $admins;
	private $researchers;
	private $subjects; 
	private $owner; //The Research who owns the UserHandler object
	private $dbq;  //The Mysql IO object to provide 
	
	function __construct($rid){
		$this->owner=$rid;
		$this->dbq= new UserSQLQueryer();
		//DEBUG: echo("The RID is $rid");
	//	var_dump($this->dbq);		
	}
	
	function getAdmins(){
		if($this->admins==null){//Checking that this information isn't already there
		$this->admins=$this->dbq->getLocalUsers($this->owner, 0);
		}
		return $this->admins;
	}//end function
	
	function getResearchers(){
		
		if($this->researchers==null){//Checking that this information isn't already there
		
		$this->researchers=$this->dbq->getLocalUsers($this->owner, 1);
		}
		return $this->researchers;
		
	}
	
	function getSubjects(){//Checking that this information isn't already there
		if($this->subjects==null){
			$this->subjects=$this->dbq->getLocalUsers($this->owner, 2);
		}
		return $this->subjects;
	}

	function addChild($child){
		
		
	}
	
	function getChild($id){
		
	}
	
	function updateChild($id, $nChild){
		
	}
	
	function listChildren(){
		if($this->inDB == true){
			$users=$this->dbq->getUsers($this->owner);
			return	$users;
		}else{
			return false;
		}
	}
	
	
	function isLocalAdmin($uid){
		if($this->dbq->isLocalAdmin($this->owner,$uid)){
			return true;
		}else{
			return false;
		}
	}
	
	function isLocalResearcher($uid){
		if($this->dbq->isLocalResearcher($this->owner,$uid)){
			return true;
		}else{
			return false;
		}
	}
	
	function isLocalSubject($uid){
		if($this->dbq->isLocalSubject($this->owner,$uid)){
			return true;
		}else{
			return false;
		}
	}
	
	function addAdmin($UID){
		if($this->isLocalAdmin($UID)){
			return true;
		}else{
			if($this->hasRights($UID)){
				if($this->dbq->setAdmin($this->owner, $UID)){
					return true;
				}else{
					return false;
				}
			}else{
				if($this->dbq->addUserRight($this->owner, $UID, true)){
					return true;
				}else{
					return false;
				}
			}
		}
	}
	
	function rmAdmin($UID){
		if($this->isLocalAdmin($UID)==false){
			return true;
		}else{
			if($this->hasRights($UID)){
				if($this->dbq->rmAdmin($this->owner, $UID)){
					return true;
				}else{
					return false;
				}
			}
		}
	}
	
	function addResearcher($UID){
		if($this->isLocalResearcher($UID)){
			return true;
		}else{
			if($this->hasRights($UID)){
				if($this->dbq->setResearcher($this->owner, $UID)){
					return true;
				}else{
					return false;
				}
			}else{
				if($this->dbq->addUserRight($this->owner, $UID, false, true)){
					return true;
				}else{
					return false;
				}
			}
		}
	}
	
	function rmResearcher($UID){
		if($this->isLocalResearcher($UID)==false){
			return true;
		}else{
			if($this->hasRights($UID)){
				if($this->dbq->rmResearcher($this->owner, $UID)){
					return true;
				}else{
					return false;
				}
			}
		}
	}
	
	function addSubject($UID){
	if($this->isLocalSubject($UID)){
			return true;
		}else{
			if($this->hasRights($UID)){
				if($this->dbq->setSubject($this->owner, $UID)){
					return true;
				}else{
					return false;
				}
			}else{
				if($this->dbq->addUserRight($this->owner, $UID, false, false, true)){
					return true;
				}else{
					return false;
				}
			}
		}
	}
	
	function rmSubject($UID){
	if($this->isLocalSubject($UID)==false){
			return true;
		}else{
			if($this->hasRights($UID)){
				if($this->dbq->rmSubject($this->owner, $UID)){
					return true;
				}else{
					return false;
				}
			}
		}
	}
	
	function hasRights($uid){
		if($this->dbq->hasRights($this->owner,$uid)){
			return true;
		}else{
			return false;
		}
	}
	
	function localPrivar($uid){
		$privar = $this->dbq->localPrivar($this->owner,$uid);
		if($privar!="SelectError"){
			return $privar;
		}else{
			return false;
		}
	}
	
	function createLocalPrivar($uid,$privar){
		$number = count($this->localPrivar($uid))+1;
		if($this->dbq->createPrivar($uid,$this->owner,$privar,$number)){
			return true;
		}else{
			return false;
		}
	}
	
	function setLocalPrivar($uid,$privar,$number){
		if($this->dbq->setPrivar($uid,$this->owner,$privar,$number)){
			return true;
		}else{
			return false;
		}
	}
	
}//end of class
?>