<?php
class ResearchSQLQueryer extends SQLQueryer{

	function getResearch($id){					//Returns the name of the research based on the ID given
		$query= "SELECT  * FROM `tbl_research` WHERE `research_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			return $native_result;				//Returns the name of the research based on the ID given
		}
	}//end function							
	
	function getName($id){					//Returns the name of the research based on the ID given
		$query= "SELECT  `research_name` FROM `tbl_research` WHERE `research_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the name of the research based on the ID given

		}
	}//End function	

	function setName($id,$name){
		$query= "UPDATE `tbl_research` SET `research_name` = '$name' WHERE `research_id` ='$id' LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}
	}//end of setName
	
	function getQueries($id){					//This function returns all queries 
												//in the research in the PHP-Mysql 
												//native result form (Returns names, locked values and IDs)
		$query= "SELECT `query_id`, `name`, `locked` FROM `tbl_query` WHERE `research_id` =$id;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			return $native_result;				//Returns the queries of the research based on the ID given
		}
	}//End function

	function getQueries0($id){					// For the fixed time research
												//This function returns all queries 
												//in the research in the PHP-Mysql 
												//native result form (Returns names, locked values and IDs)
		$query= "SELECT
				  tbl_query.query_id,
				  tbl_query.name,
				  tbl_query.locked
				 FROM
				  tbl_query,
				  tbl_query_times
				 WHERE
				  tbl_query.query_id = tbl_query_times.query_id AND
				  tbl_query.research_id = $id
				 ORDER BY
				  tbl_query_times.qtime;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			return $native_result;				//Returns the queries of the research based on the ID given
		}
	}//End function
	
	function getQueries1($id){					// For the fixed interval research
												//This function returns all queries 
												//in the research in the PHP-Mysql 
												//native result form (Returns names, locked values and IDs, if the research is fixed interval research-> ORDERed BY fixedtime
		$query= "SELECT
				  tbl_query.query_id,
				  tbl_query.name,
				  tbl_query.locked
				 FROM
				  tbl_query,
				  tbl_fixed_times
				 WHERE
				  tbl_query.query_id = tbl_fixed_times.query_id AND 
				  tbl_query.research_id = $id
				 ORDER BY
				  tbl_fixed_times.fixedtime;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			return $native_result;				//Returns the queries of the research based on the ID given
		}
	}// End function
				
	function getInfo($id){
		$this->getResearch($id);
	}// End function
		
	function createResearch($name, $descr, $collectionMethod,$startT,$endT,$qPerDay){ //This is a function to create a new database row representing a research.
		$query= "INSERT INTO `tbl_research` (`research_id` ,`research_name`, `research_descr` ,`data_collection_method`,`startTime`,`endTime`,`queriesPerDay`,`locked`) VALUES (NULL , '$name', '$descr', '$collectionMethod','$startT','$endT','$qPerDay',0);";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError"; 			//Return error if error resulted
		}else{ 								//Go get the ID of the created question		
			$query=("SELECT `research_id` FROM `tbl_research` WHERE `research_name` = '$name' AND `data_collection_method` = '$collectionMethod' AND `startTime`='$startT' ORDER BY `research_id` DESC LIMIT 1;");
			$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "SelectError";
			}else{
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				if($collectionMethod==1){
					if(!$this->createFixedTimes($row[0],$qPerDay)){
						return false;
					}
				}
				return $row[0];				//Returns the newly created research ID number in the database.
			}
		}
	}// End function
			
	function listResearchByUser($id){
		$query="SELECT re.research_id, re.research_name, re.research_descr, re.startTime, re.endTime, ur.subject, ur.researcher, ur.admin FROM `tbl_user_rights` as ur, `tbl_research` as re WHERE ur.UID= $id AND ur.research_id = re.research_id AND (ur.subject = 1 OR ur.researcher = 1 OR ur.admin = 1) ORDER BY `re`.`created` DESC;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			return $native_result;				//Returns an SQL result of researcher that a single user participates in.
		}
	}
		
	function getCollMethod($id){
		$query= "SELECT  `data_collection_method` FROM `tbl_research` WHERE `research_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the collection method of the research based on the ID given		
		}
	}
	
	function setCollMethod($id,$colmet){
		$query= "UPDATE `tbl_research` SET `data_collection_method` = '$colmet' WHERE `research_id` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}
	}
	
	function getDescr($id){
		$query= "SELECT  `research_descr` FROM `tbl_research` WHERE `research_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the description of the research based on the ID given

		}
	}
	
	function setDescr($id,$descr){
		$query= "UPDATE `tbl_research` SET `research_descr` = '$descr' WHERE `research_id` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}
	}
		
	function createTables2DB(){
		$query= "CREATE TABLE `tbl_research` (`research_id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`research_name` VARCHAR( 30 ) NOT NULL UNIQUE,`research_descr` VARCHAR( 255 ) NOT NULL,`data_collection_method` INT NOT NULL ) ENGINE = MYISAM ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}												//end function			
	}

	function getStartTime($id){					//Returns the start time of the research based on the ID given
		$query= "SELECT  `startTime` FROM `tbl_research` WHERE `research_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the start time of the research based on the ID given

		}
	}//End function	
	
	function setStartTime($id,$startTime){					//Returns the start time of the research based on the ID given
		$query= "UPDATE `tbl_research` SET `startTime` = '$startTime' WHERE `research_id` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}
	}//End function	
	
	function getEndTime($id){					//Returns the end time of the research based on the ID given
		$query= "SELECT  `endTime` FROM `tbl_research` WHERE `research_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the end time of the research based on the ID given
		}
	}//End function	
	
	function setEndTime($id,$endTime){					//Returns the start time of the research based on the ID given
		$query= "UPDATE `tbl_research` SET `endTime` = '$endTime' WHERE `research_id` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}
	}//End function	
	
	function getQueriesPerDay($id){					//Returns the amount of queries sent per day in research
		$query= "SELECT  `queriesPerDay` FROM `tbl_research` WHERE `research_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//Returns the amount of queries sent per day in research based on the given ID
		}
	}//End function	
	
	function setQueriesPerDay($id,$qPerDay){					//sets the amount of queries sent per day
		$query= "UPDATE `tbl_research` SET `queriesPerDay` = '$qPerDay' WHERE `research_id` =$id LIMIT 1 ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}
	}//End function	
		
	function getQueryNames($id){
		$query="SELECT `name` FROM `tbl_query` WHERE `research_id`='$id'";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($result)){
				return "SelectError";
			}else{
				$native_result = $result->getResource();
				return $native_result;
			}
	}//end of function getQueryNames
	
	function getStatus($id){
		$query="SELECT `startTime`,`endTime` FROM `tbl_research` WHERE `research_id`='$id' LIMIT 1";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$end = mysql_result($native_result, 0, 'endTime');
			$start = mysql_result($native_result, 0, 'startTime');
			$datenow = date('Y-m-d');
			if($datenow>$end){// $end1
				return "Ended";
			}elseif($datenow>=$start && $datenow<=$end){ // $start1 $end1
				return "On Progress";
			}elseif($start>$datenow){ // $start1
				return "Starts";
			}else{
				return false;
			}
		}
	}
	
	function getCreated($id){
		$query="SELECT `created` FROM `tbl_research` WHERE `research_id`='$id'";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($result)){
				return "SelectError";
			}else{
				$native_result = $result->getResource();
				return $native_result;
			}
	}
	
	function getLocalUsers($id){	//NOT IN USE
		
	}//end of function
	
	function addLocalUser($id,$uid){	//NOT IN USE
		
	}//end of addLocalUser
	
	function rmResearch($id){
		$queries = $this->getQueries($id);
		if(mysql_numrows($queries)>0){
			for($i=0;$i<mysql_numrows($queries);$i++){
				$queryid = mysql_result($queries,$i,"query_id");
				$query = new Query($queryid);
				$query->rmQuery();
			}
		}	
		$query= "DELETE FROM `tbl_research` WHERE `research_id` = $id LIMIT 1";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
		if(MDB2::isError($result)){							//check weather query succeeded
			return false;
		}else{
			$query= "DELETE FROM `tbl_user_rights` WHERE `research_id` = $id;";
			$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
			if(MDB2::isError($result)){							//check weather query succeeded
				return false;
			}else{
				$query= "DELETE FROM `tbl_query_times` WHERE `research_id` = $id;";
				$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
				if(MDB2::isError($result)){							//check weather query succeeded
					return false;
				}else{
					$query= "DELETE FROM `tbl_subject` WHERE `research_id` = $id;";
					$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
					if(MDB2::isError($result)){							//check weather query succeeded
						return false;
					}else{
						$query= "DELETE `tbl_text_answer` FROM `tbl_text_answer`,`tbl_answer` WHERE `tbl_answer`.`answer_id` = `tbl_text_answer`.`answer_id` AND `tbl_answer`.`research_id` = '$id';";
						$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
						if(MDB2::isError($result)){							//check weather query succeeded
							return false;
						}else{
							$query= "DELETE `tbl_num_answer` FROM `tbl_num_answer`,`tbl_answer` WHERE `tbl_answer`.`answer_id` = `tbl_num_answer`.`answer_id` AND `tbl_answer`.`research_id` = '$id';";
							$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
							if(MDB2::isError($result)){							//check weather query succeeded
								return false;
							}else{
								$query= "DELETE `tbl_media_answer` FROM `tbl_media_answer`,`tbl_answer` WHERE `tbl_answer`.`answer_id` = `tbl_media_answer`.`answer_id` AND `tbl_answer`.`research_id` = '$id';";
								$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
								if(MDB2::isError($result)){							//check weather query succeeded
									return false;
								}else{
									$query= "DELETE FROM `tbl_answer` WHERE `research_id` = '$id';";
									$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
									if(MDB2::isError($result)){							//check weather query succeeded
										return false;
									}else{
										$query= "DELETE FROM `tbl_fixed_times` WHERE `research_id` = '$id';";
										$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
										if(MDB2::isError($result)){							//check weather query succeeded
											return false;
										}else{
											$query= "DELETE FROM `tbl_fixed` WHERE `research_id` = '$id';";
											$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
											if(MDB2::isError($result)){							//check weather query succeeded
												return false;
											}else{
												return true;
											}
										}
									}	
								}	
							}	
						}	
					}	
				}		
			}		
		}				
	}//end of function
	
	function setQueryTimesQueryId($id,$query_id){
		$query= "UPDATE `tbl_query_times` SET `query_id` = '$query_id' WHERE `qtime_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError"; 			//Return error if error resulted
		}else{ 									
			return true;			
		}
	}
	
	function setQueryTimesQueryTime($id,$time){
		$query= "UPDATE `tbl_query_times` SET `qtime` = '$time' WHERE `qtime_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError"; 			//Return error if error resulted
		}else{ 									
			return true;			
		}
	}
	
	function createQueryTimes($id,$query_id=0,$time){
		$query= "INSERT INTO `tbl_query_times` (`qtime_id` ,`research_id`, `query_id` ,`qtime`) VALUES (NULL , '$id', '$query_id', '$time');";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError"; 			//Return error if error resulted
		}else{ 									
			$query= "SELECT `qtime_id` FROM `tbl_query_times` WHERE `research_id`=$id AND `qtime`='$time' LIMIT 1;";
			$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($result)){
				return "SelectError"; 			//Return error if error resulted
			}else{ 									
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return $row[0];			
			}			
		}
	}
	
	function createFixedTimes($id,$qPerDay){
		for($i=0;$i<$qPerDay;$i++){
			$j = $i+1;
			$query= "INSERT INTO `tbl_fixed_times` (`fixedtime_id` ,`research_id`,`query_id`,`fixedtime`) VALUES (NULL , '$id', 0, '$j');";
			$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($result)){
				return "InsertError"; 			//Return error if error resulted
			}
		}
		return true;	
	}
	
	function insertFixedTime($id,$query_id=0,$order){ // insert a new row with the given order into tbl_fixed_times
		$query= "INSERT INTO `tbl_fixed_times` (`fixedtime_id` ,`research_id`,`query_id`,`fixedtime`) VALUES (NULL , $id, $query_id, $order);";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError"; 			//Return error if error resulted
		}else{ 									
			return true;			
		}
	}

	function setFixedTimesQueryId($fid,$query_id){
		$query= "UPDATE `tbl_fixed_times` SET `query_id`=$query_id WHERE `fixedtime_id`=$fid LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError"; 			//Return error if error resulted
		}else{ 									
			return true;			
		}
	}
	
	//
	function getFixedTimesById($qid){
		$query= "SELECT * FROM `tbl_fixed_times` WHERE `query_id`=$qid;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "SelectError"; 			//Return error if error resulted
		}else{ 									
			$native_result = $result->getResource();
			return $native_result;			
		}	
	}
	
	function createFixed($id,$firsttime,$interval){
		$query= "INSERT INTO `tbl_fixed` (`fixed_id` ,`research_id`,`firsttime`,`interval`) VALUES (NULL , '$id', '$firsttime', '$interval');";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError"; 			//Return error if error resulted
		}else{ 									
			$query= "SELECT `fixed_id` FROM `tbl_fixed` WHERE `research_id`=$id LIMIT 1;";
			$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($result)){
				return "SelectError"; 			//Return error if error resulted
			}else{ 									
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return $row[0];			
			}			
		}
	}
	
	function getQueryTimes($id){
		$query= "SELECT
				  *
				 FROM
				  `tbl_query_times`
				 WHERE
				  `research_id`=$id
				 ORDER BY
				  `qtime` ASC;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "SelectError"; 			//Return error if error resulted
		}else{ 									
			$native_result = $result->getResource();
			return $native_result;			
		}
	}
	
	function getFixedTimes($id){
		$query= "SELECT 
				  *
				 FROM
				  `tbl_fixed_times`
				 WHERE
				  `research_id`=$id
				 ORDER BY
				  `fixedtime` ASC;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "SelectError"; 			//Return error if error resulted
		}else{ 									
			$native_result = $result->getResource();
			return $native_result;			
		}
	}
	
	function getFixedFirsttime($id){
		$query= "SELECT `firsttime` FROM `tbl_fixed` WHERE `research_id`=$id;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "SelectError"; 			//Return error if error resulted
		}else{ 									
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];						
		}
	}
	
	function setFixedFirsttime($id,$firsttime){
		$query= "UPDATE `tbl_fixed` SET `firsttime` = '$firsttime' WHERE `research_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError"; 			//Return error if error resulted
		}else{ 									
			return true;			
		}
	}
	
	function getFixedInterval($id){
		$query= "SELECT `interval` FROM `tbl_fixed` WHERE `research_id`=$id;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "SelectError"; 			//Return error if error resulted
		}else{ 									
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];						
		}
	}
	
	function setFixedInterval($id,$interval){
		$query= "UPDATE `tbl_fixed` SET `interval` = '$interval' WHERE `research_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError"; 			//Return error if error resulted
		}else{ 									
			return true;			
		}
	}
	
	function getQueryTimesById($id){
		$query= "SELECT * FROM `tbl_query_times` WHERE `qtime_id`=$id;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "SelectError"; 			//Return error if error resulted
		}else{ 									
			$native_result = $result->getResource();
			return $native_result;			
		}
	}
	
	function rmFixedTime($id){
		$query= "DELETE FROM `tbl_fixed` WHERE `research_id` = $id;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
		if(MDB2::isError($result)){							//check weather query succeeded
			return false;
		}else{
			return true;
		}		
	}
	
	function rmFixedTimeById($qid){
		$query= "DELETE FROM `tbl_fixed_times` WHERE `fixedtime_id` = $qid;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){							//check weather query succeeded
			return false;
		}else{
			return true;
		}
	}
	
	function rmQueryTimes($id){
		$query = "DELETE FROM `tbl_query_times` WHERE `research_id` = $id;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
		if(MDB2::isError($result)){							//check weather query succeeded
			return false;
		}else{
			return true;
		}		
	}
	function rmQueryTimesById($qid){
		$query= "DELETE FROM `tbl_query_times` WHERE `qtime_id` = $qid;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
		if(MDB2::isError($result)){							//check weather query succeeded
			return false;
		}else{
			return true;
		}
	}
	
	function isActive($id){
		$query= "SELECT `startTime`,`endTime` FROM `tbl_research` WHERE `research_id`=$id;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "SelectError"; 			//Return error if error resulted
		}else{ 									
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			$start = $row[0];
			/* $start = explode("-",$row[0]); */
			/* $start = ($start[0]*365)+($start[1]*31)+$start[2]; */
			/*$end = explode("-",$row[1]);
			$end = ($end[0]*365)+($end[1]*31)+$end[2];
			$datenowY = date('Y');
			$datenowM = date('m');
			$datenowD = date('d');
			$datenow = ($datenowY*365)+($datenowM*31)+$datenowD;*/
			$end = $row[1];
			$datenow = date('Y-m-d');
			if($datenow>=$start && $datenow<=$end){
				return true;
			}else{
				return false;
			}	
		}
	}
	
	function isLocked($id){
		$query= "SELECT `locked`,`endTime` FROM `tbl_research` WHERE `research_id`=$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "SelectError"; 			//Return error if error resulted
		}else{ 									
			$native_result = $result->getResource();
			$locked=mysql_result($native_result,0,"locked");
			$endTime=mysql_result($native_result,0,"endTime");
			$endTime = explode('-',$endTime);
			$endTime = ($endTime[0]*365)+($endTime[1]*31)+$endTime[2];
			$dateNowY = date('Y');
			$dateNowM = date('m');
			$dateNowD = date('d');
			$dateNow = ($dateNowY*365)+($dateNowM*31)+$dateNowD;
			if($dateNow > $endTime){
				return "Ended";
			}else{
				return $locked;
			}
		}
	}
	
	function setLocked($id,$uid){
		$query= "UPDATE `tbl_research` SET `locked` = '$uid' WHERE `research_id` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "InsertError"; 			//Return error if error resulted
		}else{ 									
			return $uid;			
		}
	}
	
	function unLock($id,$uid){
		if($this->isLocked($id)==$uid){
			$query= "UPDATE `tbl_research` SET `locked` = '0' WHERE `research_id` =$id LIMIT 1;";
			$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($result)){
				return "InsertError"; 			//Return error if error resulted
			}else{ 									
				return true;			
			}
		}else{
			return false;
		}
	}
	
	function checkOverlap($rid1,$rid2){
		$query= "SELECT DATEDIFF(`Re1`.`startTime`,`Re2`.`startTime`) FROM `tbl_research` AS Re1, `tbl_research` AS Re2 WHERE `Re1`.`research_id`='$rid1' AND `Re2`.`research_id`='$rid2';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return "SelectError"; 			//Return error if error resulted
		}else{ 									
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			if($row[0]<0){ // meaning that the actual research started before the second examined research
				$query= "SELECT DATEDIFF(`Re1`.`endTime`,`Re2`.`startTime`) FROM `tbl_research` AS Re1, `tbl_research` AS Re2 WHERE `Re1`.`research_id`='$rid1' AND `Re2`.`research_id`='$rid2';";
			}else{ // meaning that the actual research started later than the second examined research
				$query= "SELECT DATEDIFF(`Re1`.`endTime`,`Re2`.`startTime`) FROM `tbl_research` AS Re1, `tbl_research` AS Re2 WHERE `Re1`.`research_id`='$rid2' AND `Re2`.`research_id`='$rid1';";
			}
			$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($result)){
				return "SelectError"; 			//Return error if error resulted
			}else{ 									
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				if($row[0]<0){
					return false;
				}else{
					return true;
				}
			}
		}
	}
	
}														//end class
?>