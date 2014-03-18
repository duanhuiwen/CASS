<?php
class Research extends DatabaseWriteable {//implements ChildObjectOperations
		private $name;				// name of the research
		private $id;				// id number of the research
		private $descr;				// description of the research
		private $method;			// the data collection method of the research
		private $queries;			// the array of queries of the research 
		public $users;				// the array of users of the research
		private $startTime;			// the starting time of the research
		private $endTime;			// ending time
		private $queryTimes;		// storing the time of the queries
		private $queryFixedTimes;	// storing the order number of the queries
		private $queriesPerDay;		// storing how many queries are sent per day
		public $subjects;			// storing the the respondents of the research
		private $locked;			// if locked, storing a the user id of the user who locked the research
		private $created;			// storing the timestamp when the research was created
	
	function __construct($id=0){
		parent::__construct();
		$this->id=$id;
		$this->dbq= new ResearchSQLQueryer();
		$this->users = new LocalUserHandler($this->id);
		if($id!=0){
			return $this->dbGet($id);
		}
	}

	function dbGet($id){
		$temp=$this->dbq->getResearch($id);
		//var_dump( $temp);
		$num=mysql_numrows($temp);
		if($num==1){
			$this->id=mysql_result($temp,0,"research_id");
			$this->name=mysql_result($temp,0,"research_name");
			$this->descr=mysql_result($temp,0,"research_descr");
			$this->method=mysql_result($temp,0,"data_collection_method");
			$this->startTime=mysql_result($temp,0,"startTime");
			$this->endTime=mysql_result($temp,0,"endTime");
			$this->queriesPerDay=mysql_result($temp,0,"queriesPerDay");
			$this->locked=mysql_result($temp,0,"locked");
			$this->created=mysql_result($temp,0,"created");
			$this->inDB=true;
		return $this->id;
		}else return false;
	}
	
	function createResearch($name, $descr, $colmet, $uid,$sTime,$eTime,$qPerDay){
		if($colmet==2){
			$qPerDay=NULL;
		}
		$rid=$this->dbq->createResearch($name, $descr,$colmet,$sTime,$eTime,$qPerDay);
		if($rid=="InsertError" || $rid=="SelectError"){
			//echo "Error in database connection, retry! Error: $rid";
			return false;
		}else{
			$this->id = $rid;
			$this->descr = $descr;
			$this->name = $name;
			$this->method = $colmet;
			$this->startTime = $sTime;
			$this->endTime = $eTime;
			$this->queriesPerDay = $qPerDay;
			$this->locked = 0;
			$this->users = new LocalUserHandler($this->id);
			//Setting the current user as the Research Administrator
			if($this->users->addAdmin($uid)){
				return $this->id;
			}else{
				return false;
			}
		}
	}
	
	
	function getName(){
		if($this->inDB==false||$this->name==null){
			$this->name=$this->dbq->getName($this->id);
		}
		return $this->name;
	}
	
	function setName($nName){		
		if($this->inDB=true){
			if($this->dbq->setName($this->id,$nName)){
				$this->name = $nName;
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}		
	}
	
	function getDescr(){
		if($this->inDB==false||$this->descr==null){
			$this->descr=$this->dbq->getDescr($this->id); //TODO: implement this method
			//DEBUG: echo("DBQ called");
		}
		return $this->descr;
	}
	
	function setDescr($nDescr){
		if($this->dbq->setDescr($this->id,$nDescr)){
			$this->descr = $nDescr;
			return true;
		}else{
			return false;
		}
	}
	
	function getCollMethod(){		//Returns a numeric representation of the collection method
		if($this->inDB==false||$this->method==null){
			$this->method=$this->dbq->getCollMethod($this->id); 
		}
			return $this->method;
	}
	
	function setCollMethod($nMethod){		//Returns a numeric representation of the collection method
		if($this->inDB=true){
			if($this->dbq->setCollMethod($this->id,$nMethod)){
				$this->method = $nMethod;
				return true;
			}else{
				return false;
			}
		}	
	}
	
	function collMethod2String($method){ //Returns a human readable String representation of the 
		switch($method){
			case "0": return "Fixed Time";
			case "1": return "Fixed Interval";
			case "2": return "Event Contingent";
			case "3": return "Random";
		
		}
	}
	
	function getStartTime(){
		if($this->inDB==false||$this->startTime==null){
			$this->startTime=$this->dbq->getStartTime($this->id); 
			//DEBUG: echo("DBQ called");
		}
		return $this->startTime;
	}
	
	function setStartTime($nStart){
		if($this->inDB=true){
			if($this->dbq->setStartTime($this->id,$nStart)){
				$this->startTime = $nStart;
				return true;
			}else{
				return false;
			}
		}	
	}
	
	function getEndTime(){
		if($this->inDB==false||$this->endTime==null){
			$this->endTime=$this->dbq->getEndTime($this->id); 
		}
		return $this->endTime;
	}
	
	function setEndTime($nEnd){
		if($this->inDB=true){
			if($this->dbq->setEndTime($this->id,$nEnd)){
				$this->endTime = $nEnd;
				return true;
			}else{
				return false;
			}
		}	
	}
	
	function getCreated(){
		if($this->inDB==false||$this->created==null){
			$this->created=$this->dbq->getCreated($this->id);
		}
		return $this->created;
	}
	
	function getQueriesPerDay(){
		if($this->inDB==false||$this->queriesPerDay==null){
			$this->queriesPerDay=$this->dbq->getQueriesPerDay($this->id);
		}
		return $this->queriesPerDay;
	}
	
	function setQueriesPerDay($nQPerDay){
		if($this->inDB=true){
			if($this->dbq->setQueriesPerDay($this->id,$nQPerDay)){
				$this->queriesPerDay = $nQPerDay;
				return true;
			}else{
				return false;
			}
		}	
	}
	
	function isLocked(){
			$this->locked=$this->dbq->isLocked($this->id);
			return $this->locked;
	}
	
	function setLocked($uid){ //lock with user id to know which user can unlock it
		if($this->inDB=true){
			if($this->dbq->setLocked($this->id,$uid)){
				$this->locked = $uid;
				return true;
			}else{
				return false;
			}
		}	
	}
	
	function unLock($uid){
		if($this->inDB=true){
			if($this->isLocked()==$uid){
				if($this->dbq->unLock($this->id,$uid)){
					$this->locked = 0;
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}	
	}
	
	
	function getID(){
		if($this->id!=null){
			return $this->id;			
		}
		return false;
	}
	
	
	function listChildren(){ //This method will return an SQL element _listing_ all children
		if($this->inDB == true){
			$this->queries=$this->dbq->getQueries($this->id);			
			return	$this->queries;
		}else{
			return false;
		}
	}
	
	public function getChild($id){ //Returns a query type child object
		$c = new Query($id);
		return $c;
	} 
	
	function addChild($nChild){
		$this->queries->last();
		$loc=$this->queries->pos();
		$loc++;
		$this->queries[$loc]=$nChild;
		$nChild->dbq->createQuery($nChild->getName(), $this->id);
		//TODO: Make dbWrite methods to all classes, with recursive writing of evvverrrythinngg! :)
	}
	
	
	function updateChild($nChild){//Takes as an argument, the child object to be replaced. The child must contain the correct id
		
	}

	function setChildren($nChildren){
		
	}

	function dbWrite(){
		//TODO: Implement this method!
	}
	
	function dB2Child($result){
		
	}
	function clearChildren(){
		
	}
	
	function rmResearch(){
		if($this->dbq->rmResearch($this->id)){
			return true;
		}else{
			return false;
		}
	}
	
	function setQueryTimesQueryTime($qid,$time){		
		if($this->dbq->setQueryTimesQueryTime($qid,$time)){
			return true;
		}else{
			return false;
		}
	}
	
	function setQueryTimesQueryId($qid,$query_id){		
		if($this->dbq->setQueryTimesQueryId($qid,$query_id)){
			return true;
		}else{
			return false;
		}
	}
	
	function setFixedTimesQueryId($fid,$query_id){
		if($this->dbq->setFixedTimesQueryId($fid,$query_id)){
			return true;
		}else{
			return false;
		}
	}
	
	function getQueryTimes($qid=0){
		if($qid!=0){
			$this->queryTimes=$this->dbq->getQueryTimesById($qid);
		}else{
			if($this->inDB==false||$this->queryTimes==null){
				$this->queryTimes=$this->dbq->getQueryTimes($this->id); 
			}
		}
		return $this->queryTimes;
	}
	
	function getFixedTimes($qid=0){  // new function by IK
		if($qid!=0){
			$this->queryFixedTimes=$this->dbq->getFixedTimesById($qid);
		}else{
			if($this->inDB==false||$this->queryFixedTimes==null){
				$this->queryFixedTimes=$this->dbq->getFixedTimes($this->id);
			}
		}
		return $this->queryFixedTimes;
	}
	
	function rmQueryTimes($qid=0){
		if($qid!=0){
			if($this->dbq->rmQueryTimesById($qid)){
				return true;
			}else{
				return false;
			}
		}else{
			if($this->dbq->rmQueryTimes($this->id)){
				return true;
			}else{
				return false;
			}
		}
	}
	
	function rmFixedTime($qid=0){
		if($qid!=0){
			if($this->dbq->rmFixedTimeById($qid)){
				return true;
			}else{
				return false;
			}
		}else{
			if($this->dbq->rmFixedTime($this->id)){
				return true;
			}else{
				return false;
			}
		}
	}
	
	function createQueryTimes($query_id=0,$time){		
			if($this->dbq->createQueryTimes($this->id,$query_id,$time)){
				return true;
			}else{
				return false;
			}
	}
	
	function createFixed($firsttime,$interval){		
		if($this->dbq->createFixed($this->id,$firsttime,$interval)){
			return true;
		}else{
			return false;
		}
	}
	
	function insertFixedTime($query_id=0,$order){
			if($this->dbq->insertFixedTime($this->id,$query_id,$order)){
				return true;
			}else{
				return false;
			}
	}
	
	function getFixedFirsttime(){
		if($this->getCollMethod()==1){
			$tmp = $this->dbq->getFixedFirsttime($this->id);
			if($tmp!=null || $tmp!=0){
				return $tmp;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function setFixedFirsttime($firsttime){
		if($this->getCollMethod()==1){
			if($this->dbq->setFixedFirsttime($this->id,$firsttime)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function getFixedInterval(){
		if($this->getCollMethod()==1){
			$tmp = $this->dbq->getFixedInterval($this->id);
			if($tmp!=null || $tmp!=0){
				return $tmp;
			}
		}else{
			return false;
		}
	}
	
	function setFixedInterval($interval){
		if($this->getCollMethod()==1){
			if($this->dbq->setFixedInterval($this->id,$interval)){
				return true;
			}else{
				return false;
			}
		}
	}
	
	function getSubject($uid){
		$this->subjects = new Subject($uid);
	}
	
	function isActive(){
		$active = $this->dbq->isActive($this->id);
		if($active){
			return true;
		}else{
			return false;
		}
	}
	
	function freezeResearch(){
		if($this->dbq->setLocked($this->id,"freezed")){
			$this->locked = "freezed";
			return true;
		}else{
			return false;
		}
	}
	
	function getStatus(){
		$tmp = $this->dbq->getStatus($this->id);
		if($tmp!=false || $tmp!="SelectError"){
			return $tmp;
		}else{
			return false;
		}
	}
	
	function getQuery($time){
		if($this->getCollMethod()==0){
		$qtimes = $this->getQueryTimes();
		$num = mysql_numrows($qtimes);		
		for($i=0;$i<$num;$i++){
			$qtime = mysql_result($qtimes,$i,'qtime');
			$query_id = mysql_result($qtimes,$i,'query_id');		
			$qtime = explode(':',$qtime);
			$qtime = ($qtime[0]*60)+$qtime[1];
			$query = array();
			if($i==0 && $qtime>$time){
				return false;
			}else{
				if($qtime>$time){
					$qid = mysql_result($qtimes,$i-1,'query_id');
					$qtime = mysql_result($qtimes,$i-1,'qtime');
					$index = $i;
					break;
				}else{
					if($i==$num-1){
						$qtime = mysql_result($qtimes,$i,'qtime');

						$qid = $query_id;
						$index = $i+1;
						break;
					}
				}
			}
		}
		if(empty($qid)){
			return false;
		}else{
			$query = array(
						"qid" => $qid,
						"index" => $index,
						"qtime" => $qtime,
					);
			return $query;
			}
		}
	}
	
	function checkOverlap($rid){
		if($rid != $this->id){
			$temp = $this->dbq->checkOverlap($this->id,$rid);
			if($temp!="SelectError" || $temp!=null){
				return $temp;
			}else{
				return "Error";
			}
		}else{
			return false;
		}
	}

	
}//end of class


?>