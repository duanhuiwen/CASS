<?php
class Answer extends DatabaseWriteable{
	private $answer_id;
	private $research_id;
	private $uid;	
	private $query_id;
	private $question_id;			
	private $time;
	private $answer;
	private $type;
	private $answerOf;
	
	public function __construct($id=0){
			parent::__construct();
			$this->answer_id=$id;
			$this->dbq= new AnswerSQLQueryer();
			if($id!=0){
				return $this->dbGet($id);
			}
		}
		
	function dbGet($id){
			$temp=$this->dbq->getAnswer($id);
			$num=mysql_numrows($temp);
			if($num==1){
				$this->answer_id=mysql_result($temp,0,"answer_id");
				$this->research_id=mysql_result($temp,0,"research_id");
				$this->uid=mysql_result($temp,0,"UID");
				$this->query_id=mysql_result($temp,0,"query_id");
				$this->question_id=mysql_result($temp,0,"question_id");
				$this->time=mysql_result($temp,0,"time");
				$this->type = $this->dbq->getAnswerType($id);
				$this->answerOf = $this->dbq->getAnswerOf($id);
				if($this->type == 1){
					$this->answer=mysql_result($temp,0,"text");
				}elseif($this->type == 2 || $this->type == 4 || $this->type == 9){
					$this->answer=mysql_result($temp,0,"num");
				}elseif($this->type==3 || $this->type == 7 || $this->type == 8){
					$io = new FileIOHandler();
					if($io->getFiles2Db($id)){
						$this->answer=mysql_result($temp,0,"media");					
					}else{
						$this->answer=mysql_result($temp,0,"filepath");
					}
				}
				$this->inDB=true;
			return $this->answer_id;
			}else return false;
		}//end of function dbGet
		
		
	function getType(){
		if($this->inDB==false||$this->type==null){
			$temp=$this->dbq->getAnswerType($this->answer_id);
			$this->type = $temp;
		}
		return $this->type;
	}//end of getType function
	
	function addAnswer($research_id,$UID,$question_id,$time,$answer){
		//Check correct query to add answer
		$q = new Question(0,$question_id);
		$query_id = $q->getOwner();
		if(isset($query_id)){
			$this->dbq->addAnswer($research_id,$UID,$query_id,$question_id,$time,$answer);
		}
	}//end of addAnswer
	
	
	function getAnswer($id){ // [returns an array of the answer and everything related to it]
		$temp=$this->dbq->getAnswer($id);
			$num=mysql_numrows($temp);
			$values = array();
			for($i=0;$i<count($num);$i++){	
				$answer_id = mysql_result($temp,$i,"answer_id");
				$research_id=mysql_result($temp,$i,"research_id");
				$UID=mysql_result($temp,$i,"UID");
				$query_id=mysql_result($temp,$i,"query_id");
				$question_id=mysql_result($temp,$i,"question_id");
				$time=mysql_result($temp,$i,"time");
				if($this->getType() == 1){
					$answer=mysql_result($temp,$i,"text");
				}elseif($this->getType() == 2 || $this->getType() == 4 || $this->getType() == 5 || $this->getType() == 9){
					$answer=mysql_result($temp,$i,"num");
				}elseif($this->getType() == 3 || $this->getType() == 7 || $this->getType() == 8){
					$answer = mysql_result($temp,$i,"media");
				}
				$values[$i] = array(	
					'answer_id' => $answer_id,
					'research_id' => $research_id,
					'UID' => $UID,
					'query_id' => $query_id,
					'question_id' => $question_id,
					'time' => $time,
					'answer' => $answer,
				);
			}
		return $values;
	}//end of function get answer
	
	function answerOf(){
		if($this->inDB==false||$this->answerOf==null){
			$temp=$this->dbq->answerOf($this->answer_id);
			if($temp!="SelectError" || $temp!=null){
				$this->answerOf = $temp;
			}else{
				return false;
			}
		}
		return $this->answerOf;
	}//end of function answerOf
	
	function getAnswerText(){
		if($this->inDB==false||$this->answer==null){
			$type = $this->dbq->getAnswerType($this->answer_id);
			if($type==3 || $type==7 || $type==8){
				$io = new FileIOHandler();
				$this->answer=$io->MediaRead($type,$this->answer_id);
			}else{
				$temp = $this->dbq->getAnswer($this->answer_id);
				$num=mysql_numrows($temp);
					if($num==1){
						if($type==1){
							$this->answer=mysql_result($temp,0,"text");
						}elseif($type== 2 || $type==4 || $type==5 || $type==9){
							$this->answer=mysql_result($temp,0,"num");				
						}else{
							return false;
						}
					}
				}
			}
		return $this->answer;		
	}//end of getAnswerText
	
	function getAnswerer(){ //function to get the uid of the answerer
		if($this->inDB==false||$this->uid==null){
		$temp=$this->dbq->getAnswerer($this->answer_id);
			if($temp!="SelectError" || $temp!=null){
				$this->uid = $temp;
			}else{
				return false;
			}
		}
		return $this->uid;
	}//end of function
	
	function rmAnswer($id){
		if($this->dbq->rmAnswer($id)){
			return true;
		}else{
			return false;
		}
	}//end of function
	
	function getTime(){
		if($this->inDB==false||$this->time==null){
			$temp=$this->dbq->getTime($this->answer_id);
			if($temp!="SelectError" || $temp!=null){
				$this->time = $temp;
			}else{
				return false;
			}
		}
		return $this->time;
	}//end of getTime
	
	
	function getQueryID(){
		if($this->inDB==false||$this->query_id==null){
			$temp=$this->dbq->getQueryID($this->answer_id);
			if($temp!="SelectError" || $temp!=null){
				$this->query_id = $temp;
			}else{
				return false;
			}
		}
		return $this->query_id;
	}
	
	function getResearchID(){
		if($this->inDB==false||$this->research_id==null){
			$temp=$this->dbq->getResearchID($this->answer_id);
			if($temp!="SelectError" || $temp!=null){
				$this->research_id = $temp;
			}else{
				return false;
			}
		}
		return $this->research_id;
	}
	
	function getMediaFilePath(){
		$fileHandler = new FileIOHandler();
		if($fileHandler->getFiles2Db($this->answer_id)){
			return false;
		}else{
		if($this->inDB==false||$this->mediafilepath==null){
			$temp=$this->dbq->getFilePath($this->answer_id);
			if($temp!="SelectError" || $temp!=null){
				$this->mediafilepath = $temp;
			}else{
				return false;
			}
		}
		return $this->mediafilepath;
		}
	}//end of function
	
	function getMediaFileName(){
		if($this->type == 3 || $this->type == 7 || $this->type == 8){	
			$temp=$this->dbq->getFileName($this->answer_id);
			if($temp!="SelectError" || $temp!=null){
				return $temp;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}//end of function
	
	
}//end of class Answer
?>