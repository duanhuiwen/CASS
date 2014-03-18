<?php
class Subject extends DatabaseWriteable{
	private $uid;
	private $sid;
	private $bt_id;
	private $active;
	private $privar; //an array
	
	function __construct($uid=0){
		parent::__construct();
		$this->dbq= new SubjectSQLQueryer();
		if($uid!=0){
			$this->dbGet($uid);
		}
	}
	
	function dbGet($uid){
		$temp=$this->dbq->getSubject($uid);
		if( $temp != false){
			$num=mysql_numrows($temp);
		}
		
		if($num==1){
			$this->sid=mysql_result($temp,0,"subject_id");
			//$this->uid=mysql_result($temp,0,"UID");
			$this->uid = $uid;
			$this->bt_id=mysql_result($temp,0,"bt_id");
			$this->active=mysql_result($temp,0,"active");
			$this->privar = $this->dbq->getPrivar($this->sid);
			$this->inDB=true;
		return $this->sid;
		}else return false;		
	}// end of function
	
	function getSubjectID(){
		if($this->inDB==false||$this->sid==null){
			$this->sid=$this->dbq->getSubjectID($this->uid);			
		}
		return $this->sid;
	}
	
	/*
	 * This function gets an SQL array with the user ids of the respondents who have
	 * a certain Bluetooth id assigned to them. Each of the result is checked till
	 * the first set is found which is assigned to an ongoing research. This set is
	 * returned by this function.
	 */
	function getSubjectByBT($bt_id){
		$temp=$this->dbq->getSubjectByBT($bt_id);
		if($temp!="SelectError" || $temp!=null){
			if(mysql_numrows($temp)>0){
				for($i=0;$i<mysql_numrows($temp);$i++){
					$uid = mysql_result($temp,$i,'UID');
					$rid = mysql_result($temp,$i,'research_id');
					$r = new Research($rid);
					if($r->isActive()){
						$result = Array(
							'UID' => $uid,
							'research_id' => $rid,
						);
						return $result;
					}
					
				}
				if(empty($result)){
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}				
	}
	
	function getPrivar(){
		if($this->inDB==false||$this->privar==null){
		$temp=$this->dbq->getPrivar($this->sid);
			if($temp != "SelectError"){
				$this->privar = $temp;
			}else{
				return false;
			}			
		}
		return $this->privar;
	}//end of function getprivar
	
	function isActive(){
		$temp=$this->dbq->isActive($this->uid);
		if($temp != "SelectError"){
			$num = mysql_numrows($temp);
			if($num>0){
				for($i=0;$i<$num;$i++){
					$start = mysql_result($temp,$i,'Start');
					$end = mysql_result($temp,$i,'End');
					if($start<0 && $end>0){
						$active = true;
						return $active;
					}
				}
				if($active!=true){
					$active = false;
					return $active;
				}
			}else{
				return false;
			}
		}else{
			return "Error";
		}			
	}
	
	function getBt_id($rid){
		$temp=$this->dbq->getBt_id($this->uid,$rid);
		if($temp != "SelectError"){
			$this->bt_id = $temp;
			return $this->bt_id;
		}else{
			return false;
		}			
	}
	
	function getUID($bt_id,$rid){
		$temp=$this->dbq->getUID($bt_id,$rid);
			if($temp != "SelectError"){
				$this->uid = $temp;
				return $this->uid;
			}else{
				return false;
			}			
	}
	
	function setBt_id($bt_id,$rid){
		if($this->dbq->setBt_id($this->uid,$bt_id,$rid)){
			$this->bt_id = $bt_id;
			return true;
		}else{
			return false;
		}			
	}
	
	function setPrivar($privar){
		$number = count($this->getPrivar($this->sid))+1;
		if($this->dbq->setPrivar($this->sid,$privar,$number)){
			return true;
		}else{
			return false;
		}
	}
	
	function getLastAnswer($rid){
			$lastAnswer = $this->dbq->getLastAnswer($this->uid,$rid);
			if($lastAnswer!="SelectError"){
				return $lastAnswer;
			}else{
				return false;
			}
	}
	
	function getNextQuery($rid){
		$next = $this->dbq->getNextQuery($this->sid,$rid);
		if($next!="SelectError"){
			return $next;
		}else{
			return false;
		}
	}
	
	function getSurveyCount($rid){
		$sCount = $this->dbq->getSurveyCount($this->uid,$rid);
		if($sCount!="SelectError"){
			return $sCount+1;
		}else{
			return false;
		}
	}
	
	function participatingIn(){
		$temp = $this->dbq->participatingIn($this->uid);
		if($temp != "SelectError" || $temp!=null){
			return $temp;
		}else{
			return false;
		}
	}
	
	function checkBtId($bt_id,$rid){		 
		if($this->dbq->checkBtId($bt_id,$rid)){
			return true;
		}else{
			return false;
		}
	}
	
	function setFixedAnswer(){
		if($this->dbq->setFixedAnswer($this->sid)){
			return true;
		}else{
			return false;
		}	
	}
	
}//end of class
?>