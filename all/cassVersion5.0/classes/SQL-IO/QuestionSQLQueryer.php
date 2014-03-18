<?php
class QuestionSQLQueryer extends SQLQueryer{

	function getQuestion($id){							//Function to get all info of the question[returns an array] 
														//internal data. Same as GetInfo()
		$query= "SELECT * FROM `tbl_question` WHERE `question_id` = $id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			return $native_result;						//Returns the question text
														//as a Mysql native result type.
		}
	}									//End function

	function createQuestion($question, $number, $questionType, $queryID, $category){ //This is a function to create a new database row representing a question in a query.
		$query= "INSERT INTO `tbl_question` (`question_id` ,`query_id`,`question`,`question_type` ,`number`,`category`)VALUES (NULL , '$queryID', '$question', '$questionType', '$number', '$category');";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
	if(MDB2::isError($result)){
		echo "InsertError"; 								//Return error if error resulted
	}else{ 								//Go get the ID of the created question
		
		$query=("SELECT question_id FROM `tbl_question` WHERE number = $number AND query_id = $queryID ORDER BY question_id DESC LIMIT 1;");
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			echo "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the newly created question ID number in the database.
		}
	}
	}//end function

	function setQuestionText($id, $question){			//This is a function to change
														//the textual question of any question
														//in the database. Accepts
														//2 params, ID, and new value (string)
		$query= "UPDATE `tbl_question` SET `question` = '$question' WHERE `tbl_question`.`question_id` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
	if(MDB2::isError($result)){
		return false;
	}else{
		return true;
	}	
	}//end function
	
	function getQuestionText($id){					//Gets the question text based on the
													//ID value given													
		$query= "SELECT `question` FROM `tbl_question` WHERE `question_id` = $id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the question text.
		}
	}									//End function	
		
	function setNumber($id, $number){ 					//This is a function to set question number
		$query= "UPDATE `tbl_question` SET `number` = '$number' WHERE `question_id` ='$id' LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}	
	}//end function
	
	function getNumber($id){							 //This is a function to get question number
		$query= "SELECT `number` FROM `tbl_question` WHERE `question_id` = $id;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$row = Mysql_Fetch_Row($native_result);	
			return $row[0];	
		}	
	}//end function	
	
	function swapNumbers($id1, $id2){					//Function to swap to question number from q2 to q1
		$query= "UPDATE `tbl_question` q1, `tbl_question` q2 SET q1.`number` = q2.`number`, q2.`number` = q1.`number` WHERE q1.`question_id`=$id1 AND q2.`question_id`=$id2;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}		
	}
				
	function setCategory($id, $category){ 				//This is a function to change the 
														//category value of a question in the database
														//Accepts 2 parameters, the question ID value, and the new value (int)
		$query= "UPDATE `tbl_question` SET `category` = '$category' WHERE `tbl_question`.`question_id` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}	
	}//end function
	
	function getCategory($id){					//Gets the question text based on the
												//ID value given													
		$query= "SELECT `category` FROM `tbl_question` WHERE `question_id` = $id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the question text.
		}
	}									//End function
		
	function setSuperOf($id, $superOf){ 				//This is a function to change the SuperOf 
														//value of a particular question in the database
														//accepts 2 parameters, the question ID value, and the new value (int)
		$query= "UPDATE `tbl_question` SET `superOf` = '$superOf' WHERE `tbl_question`.`question_id` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query)         	//perform the query 
		or die('An unknown error occurred while updating the data'); 
	if(MDB2::isError($result)){							//check weather query succeeded
		return false;
	}else{
		return true;
	}	
	}//end function
		
	function getSuperOf($id){					//Gets the question text based on the
												//ID value given													
		$query= "SELECT `superOf` FROM `tbl_question` WHERE `question_id` = $id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the question text.
		}
	}									//End function
	
	function setQuestionType($id, $type){ 				//This is a function to change the Type 
														//value of a particular question in the database
														//accepts 2 parameters, the question ID value, and the new value (int)
		$query= "UPDATE `tbl_question` SET `question_type` = '$type' WHERE `tbl_question`.`question_id` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query)         	//perform the query 
		or die('An unknown error occurred while updating the data'); 
		if(MDB2::isError($result)){							//check weather query succeeded
			return false;
		}else{
			return true;
		}	
	}//end function
	
	function getQuestionType($id){					//Gets the question text based on the
													//ID value given													
		$query= "SELECT `question_type` FROM `tbl_question` WHERE `question_id` = $id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the question text.
		}
	}									//End function
		
	function addOption($questionID, $option, $superOf,$number){			//This is a function to add an option to a question 
																//accepts 3 parameters, the question ID value,
																//the value for the option and which category super it is
		$query= "INSERT INTO `tbl_option` (`option_id` ,`question_id` ,`option`, `superOf`,`number`) VALUES (NULL , '$questionID', '$option','$superOf','$number');";
		$result = $this->mdb2->query($query)         	//perform the query 
		or die('An unknown error occurred while updating the data'); 
		if(MDB2::isError($result)){							//check weather query succeeded
			return "InsertError";
		}else{
			return true;
		}	
	}// end of function addOption	
			
	function rmOption($id){							//This is a function to remove options
													//accepts 1 parameters, the option ID value
		$query= "DELETE FROM `tbl_option` WHERE `option_id` = $id LIMIT 1";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
		if(MDB2::isError($result)){							//check weather query succeeded
			return false;
		}else{
			return true;
		}	
	}//end of function rmOption		
		
	function getInfo($id){
		$this->getQuestion($id);
	}//end function
	
	function updateAllInfo(){ //this function writes all data to the database
		echo("");
	}//end function
	
	static function listAllResearch(){	// NOT IN USE
		include "../settings/dbsettings.php"; //This is a static function, so it needs the include in order to work.
		//echo ("mdb type is: $this->serverType ");
		var_dump($mdb_type);
	}//end function
	
	static function listResearchByUser($id){
		$query="SELECT re.research_id, re.research_name, re.research_descr, ur.subject, ur.researcher, ur.admin FROM `tbl_user_rights` as ur, `tbl_research` as re WHERE ur.UID= $id AND ur.research_id = re.research_id AND (ur.subject = 1 OR ur.researcher = 1 OR ur.admin = 1)";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
		return false;
	}else{
			$native_result = $result->getResource();
			return $native_result;				//Returns the question text.
	}
	}
	
	private function createOptionTable(){  //A function to create the question-option table, rarely needed.
		$query= " CREATE TABLE `tbl_option` (`option_id` INT NOT NULL AUTO_INCREMENT ,`question_id` INT NOT NULL ,`option` VARCHAR( 255 ) NOT NULL ,PRIMARY KEY ( `option_id` )) ENGINE = MYISAM ";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
	if(MDB2::isError($result)){
		return false;
	}else{
		return true;
	}
	}//end function
	
	private function createQuestionTable(){ 			//A function to create the question table, rarely needed.
		$query= "CREATE TABLE `tbl_question` (`question_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,`query_id` INT NOT NULL ,`question` VARCHAR( 255 ) NOT NULL ,`question_type` INT NOT NULL ,`number` INT NOT NULL ,`superOf` INT NOT NULL ,`category` INT NOT NULL) ENGINE = MYISAM ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
	if(MDB2::isError($result)){
		return false;
	}else{
		return true;
	}
	}//end function
	
	
	function createTables2DB(){							//A function to create the necessary tables
														//For storing this type of resources.
		$temp=$this->createOptionTable();
		$temp2=$this->createQuestionTable();
		if($temp==false ||$temp2==false ){
			return false;
		}else{
			return true;
		}
	}//end function
	function getAllQuestions($id){		//<- taitaapi olla turha	//Function to get ALL the question's of a spesific query 
														//internal data. Same as GetInfo()
		$query= "SELECT * FROM tbl_question WHERE tbl_question.query_id = '$id' ORDER BY `number` ASC;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			return $native_result;						//Returns the question text
														//as a Mysql native result type.

		}
	}//end of getAllQuestions function
	
	function getOptions($id){ 
		if($this->getQuestionType($id)==4 || $this->getQuestionType($id)==5 || $this->getQuestionType($id)==9  || $this->getQuestionType($id)==2 || $this->getQuestionType($id)==10){
			$query= "SELECT tbl_option.option,tbl_option.option_id,tbl_option.superOf,tbl_option.number FROM tbl_option WHERE tbl_option.question_id = $id ORDER BY `number` ASC;";
			$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "SelectError";
			}else{
				$native_result = $result->getResource();
				return $native_result;						//Returns the option text
														//as a Mysql native result type.
			}
		}else{
		 	return $this->getQuestionType($id);
		}
	}	//end of get options
	
	function getOwner($id){
		$query="SELECT `query_id` FROM `tbl_question` WHERE `question_id`='$id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			throw new Exception("SelectError"); 								//Return error if error resulted
		}else{ 
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];
		}
	}//end of getOwner
	
		function getAnswers($id){
		$query= "SELECT * FROM tbl_answer WHERE question_id = $id;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$values = array();
    		for ($a=0; $a<mysql_numrows($native_result);$a++){
        		$values[$a] = array(
	        		'question_id' => mysql_result($native_result,$a,"question_id"),
					'query_id' => mysql_result($native_result,$a,"query_id"),
	        		'answer_id' => mysql_result($native_result,$a,"answer_id"),
	        		'research_id' => mysql_result($native_result,$a,"research_id"),			
	        		'time' => mysql_result($native_result,$a,"time"),			
	        		'UID' => mysql_result($native_result,$a,"UID"),
	        	);
			}
			return $values;								//Returns all related to the questions answer
														//as an array.
		}
	
	}//end of getAswers
	
	
	function rmQuestion($id){							//This is a function to remove questions
														//accepts 1 parameters, the question ID value
		//if question is multiple choice question,the options has to be removed too
		$type = $this->getQuestionType($id);
		if($type==4 || $type==5 || $type==9 || $type==2 || $type==10){
				$options = $this->getOptions($id);
				for($i=0;$i<mysql_numrows($options);$i++){
					$oid = mysql_result($options,$i,"option_id");
					$this->rmOption($oid);
        	}
		}		
		$query= "DELETE FROM `tbl_question` WHERE `question_id` = $id LIMIT 1";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
		if(MDB2::isError($result)){							//check weather query succeeded
			return false;
		}else{
			return true;
		}				
	}//end of function rmQuestion
	
	function getParentQuestion($id,$qid,$category){
		$query = "SELECT `tbl_question`.`question_id` FROM `tbl_question`,`tbl_option`" .
				 "WHERE `tbl_question`.`query_id`='$qid' AND `tbl_question`.`question_type`='5'" .
				 "AND `tbl_question`.`question_id`=`tbl_option`.`question_id` AND `tbl_option`.`superOf`='$category' LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while selecting the data'); 
		if(MDB2::isError($result)){							//check weather query succeeded
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];
		}	
	}
	
	function updateQuestion($id,$newId){
		$query="UPDATE `tbl_question` SET `question_id`='$newId' WHERE `question_id`='$id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while selecting the data'); 
		if(MDB2::isError($result)){							//check weather query succeeded
			return "InsertError";
		}else{
			return $newId;
		}	
	}
	
}//end class
?>