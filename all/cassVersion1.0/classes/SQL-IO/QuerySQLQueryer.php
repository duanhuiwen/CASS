<?php
class QuerySQLQueryer extends SQLQueryer{

	function getQuery($id){ 
		$query = "SELECT * FROM tbl_query WHERE query_id = $id";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			throw new Exception("SelectError"); 								//Return error if error resulted
		}else{ 
			$native_result = $result->getResource();
			return $native_result;
		}
	}// end of function getQuery

	function createQuery($name, $researchID, $xml=false, $locked=false){ //This is a function to create a new database row representing a research.
		$query= "INSERT INTO `tbl_query` (`query_id` ,`research_id` ,`xml_file` ,`locked` ,`name`)VALUES (NULL , '$researchID', '$xml', '$locked', '$name');";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			throw new Exception("InsertError"); 								//Return error if error resulted
		}else{ 								//Go get the ID of the created query
			
			$query=("SELECT `query_id` FROM `tbl_query` WHERE `name` = '$name' AND `research_id` = '$researchID' ORDER BY `query_id` DESC LIMIT 1;");
			$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				throw new Exception("SelectError");
			}else{
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return $row[0];				//Returns the newly created research ID number in the database.
			}
		}
	}//end function
	
	function updateQuery($id, $rid, $locked, $name, $xmlfile=null){ //the xmlfile is assumed null as it's most likely not needed, but I'll leave it there just in case
		$query= "UPDATE `tbl_query` SET `research_id` = '$rid',
				`locked` = '$locked', `xml_file` = '$xmlfile',
				`name` = '$name' WHERE `tbl_query`.`query_id` =$id LIMIT 1 ;";
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
				
	function getInfo($id){
			$this->getQuery($id);
		}//TODO: Implement this
	
	function getSQLInsertQueryFunctionStub(){					//Documentation goes here
		$query= "";	// Not implemented
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//What does it return?.

		}
	}//End function	
	
	function getName($id){
		$query="SELECT `name` FROM `tbl_query` WHERE `query_id`='$id'";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the name string
		}	
	}//end of getName

	function setName($id,$name){
		$query= "UPDATE `tbl_query` SET `name` = '$name' WHERE `tbl_query`.`query_id` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}	
	}//end of function setName
		
	function createTables2DB(){
		$query= "CREATE TABLE `tbl_query` (
		`query_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`research_id` INT NOT NULL ,
		`xml_file` MEDIUMTEXT NULL ,
		`locked` TINYINT NOT NULL ,
		`name` VARCHAR( 50 ) NOT NULL
		) ENGINE = MYISAM ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
	if(MDB2::isError($result)){
		return false;
	}else{
		return true;
	}	
	}//end function
	
	function getXML($id){ // <- oma lis�ys NOT IN USE
	$query = "SELECT xml_file FROM tbl_query WHERE query_id = $id";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			throw new Exception("SelectError"); 								//Return error if error resulted
		}else{ 
			$native_result = $result->getResource();
			$row = Mysql_Fetch_Row($native_result);
			return $row[0];
		}
	}// end of getXML
		
	function isLocked($id){
		$query= "SELECT  `locked` FROM `tbl_query` WHERE `query_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the users id which has locked it,if it is locked
		}
	}// end of getLocked function
	
	function setLocked($id,$locked){ 
		$query = "UPDATE `tbl_query` SET `locked` = '$locked' WHERE `query_id` ='$id'";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return $locked;
		}
	}// end of setLocked
	
	function unLock($id){
		$query = "UPDATE `tbl_query` SET `locked` = '0' WHERE `query_id` ='$id'";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}
	}
	
	function getNumOfQuestions($id){
		$query="SELECT * FROM `tbl_question` WHERE `query_id`='$id'";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "SelectError";
			}else{
			$native_result = $result->getResource();
			$num = mysql_numrows($native_result);
			return $num;						//Returns the num of questions
		}		
	}
	
	
	function getQuestions($id){  
		$query= "SELECT * FROM tbl_question WHERE query_id = $id ORDER BY `number` ASC;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			return $native_result;								//Returns all related to the question
														//as an array.
		}
	
	}//end of getQuestions
	
	function getResearchOwner($id){
		$query= "SELECT tbl_auth.UID FROM tbl_auth,tbl_user_rights,tbl_query WHERE tbl_query.query_id='$id' AND tbl_user_rights.research_id = tbl_query.research_id 
				AND tbl_user_rights.researcher = '1' AND tbl_user_rights.UID=tbl_auth.UID AND tbl_auth.research_owner = '1';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0]; //returns the uid of research owner
		}
	}//end of function getResearchOwner
	
	function rmQuery($id){
		$num =  $this->getNumOfQuestions($id);
		if($num>0){
			$quest = $this->getQuestions($id);
			for($i=0;$i<mysql_num_rows($quest);$i++){
				$qid = mysql_result($quest,$i,"question_id");
				$question = new Question(0,$qid);
				$question->rmQuestion();
			}
		}
		
		$qtimes = $this->getQueryTime($id); // if so, remove from the tbl_query_times
		if(mysql_numrows($qtimes)>0){
			$sql = new ResearchSQLQueryer();
			for($i=0;$i<mysql_numrows($qtimes);$i++){
				$qtime_id = mysql_result($qtimes,$i,"qtime_id");
				$sql->setQueryTimesQueryId($qtime_id,0);
			}
		}
		
		$fixedtimes = $this->getFixedTime($id); // if so, remove from the tbl_fixed_times
		if(mysql_numrows($fixedtimes)>0){
			$sql = new ResearchSQLQueryer();
			for($i=0;$i<mysql_numrows($fixedtimes);$i++){
				$fixedtime_id = mysql_result($fixedtimes,$i,"fixedtime_id");
				$sql->setFixedTimesQueryId($fixedtime_id,0);
			}
		}
		
		$query= "DELETE FROM `tbl_query` WHERE `query_id` = $id LIMIT 1";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
		if(MDB2::isError($result)){							//check wether query succeeded
			return false;
		}else{
			return true;
		}	
	}//end of function rmQuery
	
	function getOwner($id){
		$query= "SELECT  `research_id` FROM `tbl_query` WHERE `query_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns research id based on the given ID
		}
	}
	
	function getQueryTime($id){
		$query= "SELECT * FROM `tbl_query_times` WHERE `query_id` =$id;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			return $native_result;				//Returns time when query is sent daily
		}
	}
	
	function getFixedTime($id){
		$query= "SELECT * FROM tbl_fixed_times WHERE query_id =$id;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			return $native_result;				//Returns at which place query is sent daily
		}
		
	}
	
	function getQuestionByNumber($id,$number){
		$query= "SELECT `question_id` FROM `tbl_question` WHERE `query_id` =$id AND `number`=$number;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns question id by question number and query id
		}
	}
}														//end class
?>