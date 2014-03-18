<?php
class EventLogger{
	private $user;
	private $ip;
	private $dbq;
	
	function __construct($uid=0){
		$this->dbq = new LogSQLQueryer();
		$this->ip = $this->getIP();
		$this->user = $uid;
	}
	
	
	function createLogItem($descr, $dateTime=0){
			$temp = $this->dbq->addItem($this->user, $this->ip, $descr, $dateTime);
		return $temp;
	}
	
	
	
	function createLogListByDate($datetime = 0, $user = "all"){
		if($user=="all"){
			$temp = $this->dbq->getLogListByDate($datetime);
			return $temp;
		}else{
			//Todo, make another query for getting info with user AND date..
		}
		
	}

	private function getIP(){
		$this->ip=$_SERVER['REMOTE_ADDR'];   
		return $this->ip;
	}
	
	function getAuthUid(){
		require_once("../common/auth_start.php");
		if(!empty($a)){
			return $a->getAuthData('uid');
		}else{
			return 0;
		}
	}
}
?>