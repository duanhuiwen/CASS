<?php
class Question extends DatabaseWriteable{
	private $questionText;
	private $type;
	private $category;
	private $superOf;
	private $owner;
	private $options;
	private $number;
	private $answers; //answers in an array
	
	public function __construct($query_id=0, $id=0){
		if(($query_id ==0 XOR $id ==0)||($query_id !=0 XOR $id !=0)){ //Let's make sure both arguments are not given
		parent::__construct();
		$this->id=$id;
		$this->owner=$query_id;
		$this->dbq= new QuestionSQLQueryer();
		if($id!=0){
			return $this->dbGet($id);
		}}else{//Let's make sure both arguments are not given
			throw new Exception("Parameter values incorrect, cannot use two arguments");
		}
	}
	
	function dbGet($id){
		$temp=$this->dbq->getQuestion($id);
		//var_dump( $temp);
		$num=mysql_numrows($temp);
		if($num==1){
			$this->id=mysql_result($temp,0,"question_id");
			$this->questionText=mysql_result($temp,0,"question");
			$this->owner=mysql_result($temp,0,"query_id");
			$this->type=mysql_result($temp,0,"question_type");
			$this->number=mysql_result($temp,0,"number");
			$this->category=mysql_result($temp,0,"category");
			
			$this->inDB=true;
		return $this->id;
		}else return false;
	}
					
	function getQuestionText(){
		if($this->inDB==false||$this->questionText==null){
			$temp=$this->dbq->getQuestionText($this->id);
			if($temp != "SelectError"){
				$this->questionText = $temp;
			}else{
				return false;
			}			
		}
		return $this->questionText;
	}//end of function getQuestionText
	
	function setQuestionText($questionText){
		$this->dbq->setQuestionText($this->id,$questionText);
	}//end of setQuestionText
	
	function getQuestionType(){
		if($this->inDB==false||$this->type==null){
			$temp=$this->dbq->getQuestionType($this->id);
			if($temp != "SelectError"){
				$this->type = $temp;
			}else{
				return false;
			}
		}
		return $this->type;
	}//end of function getQuestionType
	
	function getQuestionTypeInText(){
		$this->type = $this->getQuestionType();
		switch($this->type){
			case "1": $typeInTxt = "Open text"; break;
			case "2": $typeInTxt = "Open number"; break;
			case "3": $typeInTxt = "Sound"; break;
			case "4": $typeInTxt = "Multiple choice"; break;
			case "5": $typeInTxt = "Super"; break;
			case "6": $typeInTxt = "Comment"; break;
			case "7": $typeInTxt = "Photo"; break;
			case "8": $typeInTxt = "Video"; break;
			case "9": $typeInTxt = "Slider"; break;
			case "10": $typeInTxt = "Multiple answer"; break;
		}
		if(isset($typeInTxt)){
			return $typeInTxt;
		}else{
			return false;
		}
	}//end of function getQuestionType

	function setQuestionType($type){
		if($this->inDB==false||$this->type==null){
			$this->dbq->setQuestionType($this->id,$type);
			if($temp != "SelectError"){
					$this->type = $temp;
				}else{
					return false;
				}
			}		
		return $this->type;
	}//end of setQuestionType
	
	function getCategory(){
		if($this->inDB==false||$this->category==null){
			$temp=$this->dbq->getCategory($this->id);
			if($temp != "SelectError"){
				$this->category = $temp;
			}else{
				return false;
			}
		}
		return $this->category;
	}//end of function getCategory

	function setCategory($category){
		$this->dbq->setCategory($this->id,$category);
	}//end of setCategory
	
	function getNumber(){
		if($this->inDB==false||$this->number==null){
			$temp=$this->dbq->getNumber($this->id);
			if($temp != "SelectError"){
				$this->number = $temp;
			}else{
				return false;
			}
		}
		return $this->number;
	}//end of function getNumber

	function setNumber($number){
		$this->dbq->setNumber($this->id,$number);
	}//end of setNumber
	
	function getSuperOf($id){   //Tarpeellinen? Ei.
		if($this->inDB==false||$this->superOf==null){
			$temp=$this->dbq->getSuperOf($id);
			if($temp != "SelectError"){
				$this->superOf = $temp;
			}else{
				return false;
			}
		}
		return $this->superOf;
	}//end of getSuperOf
		
	function addOption($option,$super){
		$this->options = $this->getOptions();		
		$number = count($this->options)+1;
		if($this->dbq->addOption($this->id,$option,$super,$number)){
			return true;
		}else{
			return false;
		}
	}

	function getOptions(){ //function to get options,returns an array
			$temp=$this->dbq->getOptions($this->id);
			if($temp=="SelectError" || $temp==null){
				return false;
			}else{				
				$values = array();
    			for ($i=0; $i<mysql_num_rows($temp); ++$i){
        			$values[$i] = array(
        			'option' => mysql_result($temp,$i,"option"),
        			'id' => mysql_result($temp,$i,"option_id"),
        			'super_of' => mysql_result($temp,$i,"superOf"),
        			'number' => mysql_result($temp,$i,"number")
        			);
        		}
			$this->options = $values;
			}
		//}
		return $this->options;
	} // End of function getOptions

	function swap($id2){ //this function is used to alter the order of the questions...
		if($this->dbq->swapNumbers($this->id, $id2)){
			return true;
		}else{
			return false;
		}
	}
	
	function createQuestion($question, $questionType, $category,$number=0){
			$query = new Query($this->owner);
			if($number==0){
				$number = $query->getNumOfQuestions()+1;
			} 
			$temp=$this->dbq->createQuestion($question, $number, $questionType, $this->owner, $category);
			if($temp!="InsertError"||$temp!=null){
				$this->id=$temp;
				$this->inDB =true;
				$this->question=$question;
				$this->type=$questionType;
				$this->number=$number;
				$this->category=$category;
				return $this->id;
			}else{
				return false;
			}
	}
	
	function rmOption($id){
		if($this->dbq->rmOption($id)){
			return true;
		}else{
			return false;
		}
	}//end of function rmOption
	
	function getQuestionArray($id){  //Gets all the questions of a query and return them in an array
		$temp=$this->dbq->getAllQuestions($id);
		if($temp=="SelectError"){
			return $temp;
		}else{
			$values = array();
    	for ($i=0; $i<mysql_num_rows($temp); ++$i){
        	$values[$i] = array(
        		'question_id' => mysql_result($temp,$i,"question_id"),
				'query_id' => mysql_result($temp,$i,"query_id"),
        		'question' => mysql_result($temp,$i,"question"),
        		'question_type' => mysql_result($temp,$i,"question_type"),			
        		'number' => mysql_result($temp,$i,"number"),			
        		'category' => mysql_result($temp,$i,"category"),
        	);
		}
		return $values;
		}
	}// end of function dbGetAll
	
	
	function getQuestion($id){ 		
		return $this->dbGet();
	}//end of function getQuestion
	
	function getOwner(){	//Returns the query where question belongs to
		if($this->inDB==false||$this->owner==null){
			$temp=$this->dbq->getOwner($this->id);
			if($temp =!"SelectError"){
				$this->owner = $temp;
			}else{
				return false;				
			}	
		}
		return $this->owner;
	}//end of getOwner

	function listChildren(){
		if($this->inDB == true){
			$this->answers=$this->dbq->getAnswers($this->id);
			return	$this->answers;
		}else{
			return false;
		}
	}
	
	function rmQuestion(){
		if($this->dbq->rmQuestion($this->id)){
			return true;
		}else{
			return false;
		}
	}
	
	function getParentQuestion(){
		$cat = $this->getCategory();
		if($cat!=0){
			$tmp = $this->dbq->getParentQuestion($this->id,$this->owner,$cat);
			if($tmp!="SelectError"){
				return $tmp;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function updateQuestion($newId){
		$tmp = $this->dbq->updateQuestion($this->id,$newId);
		if($tmp!="InsertError"){
			$this->id = $newId;
			return $this->id;
		}else{
			return false;
		}
	}

}//end of class
?>