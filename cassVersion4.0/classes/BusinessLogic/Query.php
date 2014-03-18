<?php
class Query extends DatabaseWriteable{
	private $name; //Name String
	private $questions; //Array of Object: Question
	private $type; //Integer, (?) TODO: Find out what the hell is this?
	private $id; //ID int
	private $owner; //research id Int
	private $xml_file; //file
	private $locked; //0 locked,1 not locked
	private $num; //number of questions int
	private $researchOwner;
	private $isVisualize;
		
	public function __construct($id=0){
		parent::__construct();
		$this->id=$id;
		$this->dbq= new QuerySQLQueryer();
		if($id!=0){
			return $this->dbGet($id);
		}		
	}
		
	public function updateQuery(){
	}
	
	function dbGet($id){
		$temp=$this->dbq->getQuery($id);
		$num=mysql_numrows($temp);
		if($num==1){
			$this->id=mysql_result($temp,0,"query_id");//data row field
			$this->name=mysql_result($temp,0,"name");
			$this->owner=mysql_result($temp,0,"research_id");
			$this->xml_file=mysql_result($temp,0,"xml_file");
			$this->locked=mysql_result($temp,0,"locked");
			$this->researchOwner = $this->dbq->getResearchOwner($this->id);
			$this->num = $this->dbq->getNumOfQuestions($this->id);
			$this->questions = $this->dbq->getQuestions($this->id);
			$this->inDB=true;
			$this->isVisualize =mysql_result($temp,0,"visualize");
		return $this->id;
		}else return false;
	}// end of function dbGet
	
	function isVisualize(){
		return $this->isVisualize;
	}
	
    function updateQueryVisualization($visualize){
    	
    	if($this->dbq->updateQueryVisualization($this->id,$visualize)){
			return true;   		
    	}else{
    		return false;	
    	}
    	
    }
	
	function getName(){
		if($this->inDB==false||$this->name==null){
			$temp=$this->dbq->getName($this->id);
			if($temp =!"SelectError"){
				$this->name = $temp;
			}else{
				return false;
			}
		}
		return $this->name;			
	}//end of getName

	function setName($name){
		if($this->dbq->setName($this->id,$name)){
			return true;
		}else{
			return false;
		}
	}//end of function setName
	
	function getNumOfQuestions(){
		if($this->inDB==false||$this->num==null){
			$temp=$this->dbq->getNumOfQuestions($this->id);
			if($temp =!"SelectError"){
				$this->num = $temp;
			}else{
				return false;
			}	
		}
		return $this->num;	
	}//end of function getNum
	
	function getQuestionList($id){ // <- oma lis�ys(TURHA:Sama kuin query->listChildren)
		$temp = $this->dbq->getQuestions($id);
		if($temp=="SelectError"){
			return $temp;
		}else{
			$values = array();
    		for ($i=0; $i<mysql_numrows($temp); ++$i){
        		$values[$i] = array(
	        		'question_id' => mysql_result($temp,$i,"question_id"),
					'query_id' => mysql_result($temp,$i,"query_id"),
	        		'question' => mysql_result($temp,$i,"question"),
	        		'question_type' => mysql_result($temp,$i,"question_type"),			
	        		'number' => mysql_result($temp,$i,"number"),
					'superOf' => mysql_result($temp,$i,"superOf"),			
	        		'category' => mysql_result($temp,$i,"category"),
	        	);
			}
			$this->questions = $values;
		return $this->questions;
		}
	}	//end of function getQuestionList
	
	function listChildren(){
		if($this->inDB == true){
			$this->questions=$this->dbq->getQuestions($this->id);
			return	$this->questions;
		}else{
			return false;
		}		
	}// end of function listChildren
	
	function getChild($id){
		$q = new Question(0,$id);
		return $q;
	}
	
	function addChild($nChild){
	}
	
	function setChildren($nChildren){
	}
	
	function isLocked(){
		if($this->inDB==false||$this->locked==null){
			$temp=$this->dbq->isLocked($this->id);
			if($temp =!"SelectError"){
				$this->locked = $temp;
			}else{
				return false;
			}	
		}
		return $this->locked;
	}//end of getLocked
	
	function setLocked($uid){
		$temp = $this->dbq->setLocked($this->id,$uid);
		if($temp!="SelectError"){
				$this->locked = $temp;
				return $this->locked;
			}else{
				return false;
			}	
	}//end of function setlocked
	
	function unLock($uid){
		if($this->dbq->unLock($this->id,$uid)){
			$this->locked = 0;
			return true;
		}else{
			return false;
		}
	}
	
	function getOwner(){
		if($this->inDB==false||$this->owner==null){
			$temp=$this->dbq->getOwner($this->id);
			if($temp =!"SelectError"){
				$this->owner = $temp;
			}else{
				return false;
			}	
		}
		return $this->owner;
	}
	
	
	function getResearchOwner(){
		if($this->inDB==false||$this->researchOwner==null){
			$temp=$this->dbq->getResearchOwner($this->id);
			if($temp =!"SelectError"){
				$this->researchOwner = $temp;
			}else{
				return false;
			}	
		}
		return $this->researchOwner;
	}//end of function getResearchOwner
	
	function createQuery($name,$researchID,$xml=null,$locked=false){
	$qid=$this->dbq->createQuery($name, $researchID,$xml,$locked);
		if($qid=="InsertError" || $qid=="SelectError"){
			echo "Error in database connection, retry! Error: $qid";
			return false;
		}else{		
			$this->id = $qid;
			$this->name = $name;
			$this->owner = $researchID;
			$this->xml = $xml;
			$this->locked = $locked;
			return $this->id;
		}
	}
	
	function rmQuery(){
		if($this->dbq->rmQuery($this->id)){
			return true;
		}else{
			return false;
		}
	}
	
	function copyQuery($queryID){
		$oldQuery = $this->id;
		$qr = new Query($queryID);
		$numOfque = $qr->listChildren();
		$numOfque = mysql_numrows($numOfque);	
		if(isset($queryID)){
			$sql = new QuerySQLQueryer();
			$qsql = new QuestionSQLQueryer();
			$questi = $sql->getQuestions($oldQuery);
			for($i=0;$i<mysql_numrows($questi);$i++){
				$questionID = mysql_result($questi,$i,"question_id");
				$question = mysql_result($questi,$i,"question");
				$qType=mysql_result($questi,$i,"question_type");
				$qNumber=mysql_result($questi,$i,"number");
				$qCategory=mysql_result($questi,$i,"category");
				$nQuestion = new Question($queryID);
				if($numOfque>0){
					$qNumber = $numOfque+$i+1;
				}
				$qid = $nQuestion->createQuestion($question, $qType, $qCategory,$qNumber);
				if(isset($qid)){
					if($qType==4 || $qType==5 || $qType==9 || $qType==2 || $qType==10){									
						$opts = $qsql->getOptions($questionID);
						for($j=0;$j<mysql_numrows($opts);$j++){
							$op = mysql_result($opts,$j,"option");
							$supa = mysql_result($opts,$j,"superOf");
							$number = mysql_result($opts,$j,"number");
							$qsql->addOption($qid,$op,$supa,$number);
						}
					}
				}
			}
			return true;
		}else{
			return false;
		}
	}//end of function
	
	function getQueryTime(){
		if($this->inDB==true){
			return $this->dbq->getQueryTime($this->id);
		}else{
			return false;
		}
	}
	
	function getQuestionByNumber($number){
		$qnumber = $this->dbq->getQuestionByNumber($this->id,$number);
		if($qnumber!="SelectError"){
			return $qnumber;
		}else{
			return false;
		}
	}
	
	
}//end of class
?>