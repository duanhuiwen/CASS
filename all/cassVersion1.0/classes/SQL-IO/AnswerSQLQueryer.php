<?php
class AnswerSQLQueryer extends SQLQueryer{
	
	function addAnswer($research_id,$UID,$query_id,$question_id,$time,$answer){
		//selecting the type of answer(text,num or media) that answer can be inserted in correct table
		$queryType="SELECT `question_type` FROM `tbl_question` WHERE `question_id`='$question_id';";
		$resultType = $this->mdb2->query($queryType) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($resultType)){
				throw new Exception("SelectError"); 								//Return error if error resulted
		}else{
			$native_resultType = $resultType->getResource();
			$rowType=Mysql_Fetch_Row($native_resultType);
			$type = $rowType[0]; 
			//Inserting data to table tbl_answer
			$queryInsert= "INSERT INTO `tbl_answer` (`answer_id` ,`research_id` ,`UID` ,`query_id` ,`question_id` ,`time`)VALUES (NULL , '$research_id','$UID','$query_id','$question_id','$time');";
			$resultInsert = $this->mdb2->query($queryInsert) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($resultInsert)){
				throw new Exception("InsertError"); 								//Return error if error resulted
			}else{
				//if insert was success.selecting the newly made answer id 
				$queryAnswerID=("SELECT `answer_id` FROM `tbl_answer` WHERE `question_id` = '$question_id' AND `research_id` = '$research_id' AND `UID` = '$UID' AND `time` = '$time' ORDER BY `answer_id` DESC LIMIT 1;");
				$resultAnswerID = $this->mdb2->query($queryAnswerID) or die('An unknown error occurred while checking the data');
				if(MDB2::isError($resultAnswerID)){
					throw new Exception("SelectError");
				}else{
					$native_resultID = $resultAnswerID->getResource();
					$rowAnswerID=Mysql_Fetch_Row($native_resultID);
					//Inserting data to tbl_text,tbl_num or tbl_media
					switch($type){
						case 1:
							$query="INSERT INTO `tbl_text_answer` (`text_id` ,`answer_id` ,`text`)VALUES (NULL , '$rowAnswerID[0]','".utf8_decode($answer)."');";							
							break;
						case 2:
							$query="INSERT INTO `tbl_num_answer` (`num_id` ,`answer_id` ,`num`)VALUES (NULL , '$rowAnswerID[0]','$answer');";
							break;
						case 3:
							/*$io = new FileIOHandler();
							$io->mediaWrite($answer,3,$rowAnswerID[0]);*/
							//have to check if instead of audio is sent text
							$extension = substr($answer,-4);
							if($extension==".amr"){
								$query="INSERT INTO `tbl_media_answer` (`media_id` ,`answer_id` ,`media`,`filepath`,`filename`)VALUES (NULL , '$rowAnswerID[0]',NULL,NULL,'$answer');";
							}else{
								$query="INSERT INTO `tbl_text_answer` (`text_id` ,`answer_id` ,`text`)VALUES (NULL , '$rowAnswerID[0]','$answer');";
							}
							break;
						case 4:
							if(is_numeric($answer)){
								$query="INSERT INTO `tbl_num_answer` (`num_id` ,`answer_id` ,`num`)VALUES (NULL , '$rowAnswerID[0]','$answer');";
							}elseif(is_string($answer)){
								$query="INSERT INTO `tbl_text_answer` (`text_id` ,`answer_id` ,`text`)VALUES (NULL , '$rowAnswerID[0]','$answer');";
							}
							break;
						case 5:
							$query="INSERT INTO `tbl_num_answer` (`num_id` ,`answer_id` ,`num`)VALUES (NULL , '$rowAnswerID[0]','$answer');";
							break;							
						case 7:
							/*$io = new FileIOHandler();
							$io->mediaWrite($answer,7,$rowAnswerID[0]);*/
							$query="INSERT INTO `tbl_media_answer` (`media_id` ,`answer_id` ,`media`,`filepath`,`filename`)VALUES (NULL , '$rowAnswerID[0]',NULL,NULL,'$answer');";
							break;
						case 8: 
							/*$io = new FileIOHandler();
							$io->mediaWrite($answer,8,$rowAnswerID[0]);*/
							$query="INSERT INTO `tbl_media_answer` (`media_id` ,`answer_id` ,`media`,`filepath`,`filename`)VALUES (NULL , '$rowAnswerID[0]',NULL,NULL,'$answer');";
							break;
						case 9:
							$query="INSERT INTO `tbl_num_answer` (`num_id` ,`answer_id` ,`num`)VALUES (NULL , '$rowAnswerID[0]','$answer');";
							break;	
						case 10:
							/*if(is_numeric($answer)){
								$query="INSERT INTO `tbl_num_answer` (`num_id` ,`answer_id` ,`num`)VALUES (NULL , '$rowAnswerID[0]','$answer');";
							}elseif(is_string($answer)){*/
								$query="INSERT INTO `tbl_text_answer` (`text_id` ,`answer_id` ,`text`)VALUES (NULL , '$rowAnswerID[0]','$answer');";
							//}
							break;				 		
					}
					//variable query is set
					if(isset($query)){
						$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
						if(MDB2::isError($result)){
							throw new Exception("InsertError"); 								//Return error if error resulted
						}else{
							return true;
						}
					}
				}
			}
		}		
	}//end of question
	
	function rmAnswer($answer_id){
		$query = "DELETE FROM `tbl_answer` WHERE `answer_id` = '$answer_id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while removing the data');
		if(MDB2::isError($result)){
			throw new Exception("DeleteError"); 								//Return error if error resulted
		}else{ 			
			return true;
		}
	}//end of function

	
	function createTables2DB(){
		$query= "CREATE TABLE `tbl_answer` (
		`answer_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`research_id` INT NOT NULL ,
		`UID` INT NOT NULL ,
		`query_id` INT NOT NULL ,
		`question_id` INT NOT NULL ,
		`time` VARCHAR( 20 ) NOT NULL
		) ENGINE = MYISAM ;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;
		}	
	}//end of function

	function getAnswerType($id){
		$query="SELECT question_type FROM tbl_question,tbl_answer WHERE tbl_question.question_id=tbl_answer.question_id AND tbl_answer.answer_id=$id LIMIT 1";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];
		}
	}//end of getAnswerType
	
	
	function getAnswer($id){
		$type = $this->getAnswerType($id);
		if($type==1){
			$query= "SELECT  tbl_answer.*,tbl_text_answer.text FROM tbl_answer,tbl_text_answer WHERE tbl_answer.answer_id =$id AND tbl_text_answer.answer_id = tbl_answer.answer_id;";
		}elseif($type==2||$type==4|| $type==5 ||$type==9){
			$query= "SELECT  tbl_answer.*,tbl_num_answer.num FROM tbl_answer,tbl_num_answer WHERE tbl_answer.answer_id =$id AND tbl_num_answer.answer_id = tbl_answer.answer_id;";
		}elseif($type==3||$type==7||$type==8){
			$io = new FileIOHandler();
			//return $io->mediaRead($type,$id);
			if($io->getFiles2Db($id)){
				$query= "SELECT  tbl_answer.*,tbl_media_answer.media FROM tbl_answer,tbl_media_answer WHERE tbl_answer.answer_id =$id AND tbl_media_answer.answer_id = tbl_answer.answer_id;";
			}else{
				$query= "SELECT  tbl_answer.*,tbl_media_answer.filepath FROM tbl_answer,tbl_media_answer WHERE tbl_answer.answer_id =$id AND tbl_media_answer.answer_id = tbl_answer.answer_id;";
			}		
		}else{
			return false;
		}
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			return $native_result;				//Returns the answer on the ID given
		}
	}//end of function getAnswer
	
	function getInfo($id){
		$this->getAnswer($id);
	}//end function
	
	function getAnswerOf($id){
		$query="SELECT `question_id` FROM `tbl_answer` WHERE `answer_id`='$id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "SelectError";
			}else{
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return $row[0];				//Returns the question_id on the answer ID given
		}
	}
	
	function getAnswerer($id){
		$query="SELECT `UID` FROM `tbl_answer` WHERE `answer_id`='$id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "SelectError";
			}else{
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return $row[0];				//Returns the question_id based on the given answer ID 
			}
		}//end of getAnswerer	
			
	function getTime($id){
		$query="SELECT `time` FROM `tbl_answer` WHERE `answer_id`='$id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "SelectError";
			}else{
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return $row[0];				//Returns the time based on the given ID
			}
	}

	function getQueryID($id){
		$query="SELECT `query_id` FROM `tbl_answer` WHERE `answer_id`='$id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "SelectError";
			}else{
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return $row[0];				//Returns the time based on the given ID
			}
	}//end of function
	
	function getResearchID($id){
		$query="SELECT `research_id` FROM `tbl_answer` WHERE `answer_id`='$id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "SelectError";
			}else{
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return $row[0];				//Returns the time based on the given ID
			}
	}//end of function
	
	
	function getFilePath($id){
		$query="SELECT `filepath` FROM `tbl_media_answer` WHERE `answer_id`='$id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "SelectError";
			}else{
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return $row[0];				//Returns the time based on the given ID
			}
	}//end of function
	
	function getFileName($id){
		$query="SELECT `filename` FROM `tbl_media_answer` WHERE `answer_id`='$id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "SelectError";
			}else{
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return $row[0];				//Returns the time based on the given ID
			}
	}//end of function
	
	function getOptionText($id){
		$query="SELECT `option` FROM `tbl_option` WHERE `option_id`='$id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "SelectError";
			}else{
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return $row[0];				//Returns the option text of the given ID
			}
	}//end of function
	
	function getResearchAnswers($rid){
		$query = "SELECT `a`.`research_id`, `a`.`query_id`, `a`.`question_id`, `a`.`answer_id`, `auth`.`username`, `a`.`time`,`q`.`number`, `q`.`question`,CASE `q`.`question_type`
			WHEN 1 THEN (SELECT `atext`.`text` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_text_answer` AS `atext` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `atext`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 2 THEN (SELECT `anum`.`num` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `anum`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 3 THEN (SELECT IF(ISNULL((SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`))>0,(SELECT `atext`.`text` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_text_answer` AS `atext` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `atext`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`),(SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)))
			WHEN 4 THEN (SELECT `o`.`option` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum`, `tbl_option` AS `o` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`aid`.`question_id` AND `anum`.`answer_id`=`aid`.`answer_id` AND `o`.`question_id`=`q`.`question_id` AND `anum`.`num`=`o`.`option_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 5 THEN (SELECT `o`.`option` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum`, `tbl_option` AS `o` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`aid`.`question_id` AND `anum`.`answer_id`=`aid`.`answer_id` AND `o`.`question_id`=`q`.`question_id` AND `aid`.`answer_id`=`a`.`answer_id` AND `anum`.`num`=`o`.`option_id`)
			WHEN 7 THEN (SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 8 THEN (SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 9 THEN (SELECT `anum`.`num` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `anum`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 10 THEN (SELECT `atext`.`text` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_text_answer` AS `atext` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `atext`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			END AS `answer`, `q`.`question_type` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `a`,`tbl_auth` AS `auth` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `a`.`UID`=`auth`.`UID` AND `r`.`research_id`='$rid' ORDER BY auth.username, `a`.`time`, `qr`.`query_id`, `q`.`question` ASC;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			return $native_result;				
		}
	}
	
	function getResearchAnswers3($rid){
		$query = "SELECT `a`.`research_id`, `a`.`query_id`, `a`.`question_id`, `a`.`answer_id`, `auth`.`username`, `a`.`time`,`q`.`number`, `q`.`question`,CASE `q`.`question_type`
			WHEN 1 THEN (SELECT `atext`.`text` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_text_answer` AS `atext` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `atext`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 2 THEN (SELECT `anum`.`num` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `anum`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 3 THEN (SELECT IF(ISNULL((SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`))>0,(SELECT `atext`.`text` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_text_answer` AS `atext` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `atext`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`),(SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)))
			WHEN 4 THEN (SELECT `o`.`option` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum`, `tbl_option` AS `o` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`aid`.`question_id` AND `anum`.`answer_id`=`aid`.`answer_id` AND `o`.`question_id`=`q`.`question_id` AND `anum`.`num`=`o`.`option_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 5 THEN (SELECT `o`.`option` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum`, `tbl_option` AS `o` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`aid`.`question_id` AND `anum`.`answer_id`=`aid`.`answer_id` AND `o`.`question_id`=`q`.`question_id` AND `aid`.`answer_id`=`a`.`answer_id` AND `anum`.`num`=`o`.`option_id`)
			WHEN 7 THEN (SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 8 THEN (SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 9 THEN (SELECT `anum`.`num` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `anum`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 10 THEN (SELECT `atext`.`text` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_text_answer` AS `atext` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `atext`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			END AS `answer`, `q`.`question_type` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `a`,`tbl_auth` AS `auth` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `a`.`UID`=`auth`.`UID` AND `r`.`research_id`='$rid' ORDER BY auth.username, `a`.`time`, `qr`.`query_id`, `q`.`number` ASC;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			return $native_result;				
		}
	}
	
	function getResearchQuestions($rid){	// Gets all the questions.
		$query = 	"SELECT
						tbl_question.question
					FROM
						tbl_question, tbl_research, tbl_query
					WHERE
						tbl_question.query_id = tbl_query.query_id
						AND tbl_research.research_id = tbl_query.research_id
						AND tbl_research.research_id = $rid
					ORDER BY
						tbl_question.question;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			return $native_result;				
		}
		
	}	// End of function getResearchQuestion
	
	function getResearchQuestionsInOrder0($rid){	/* This query returns all the queries in the
	                                            	 * order as they appear in time after each other
													 * on the respondents phone when the data
													 * collection method is 0 (fixed time) */
													 									
		$query =	"SELECT
						tbl_question.question,
						tbl_question.query_id,
						tbl_question.number,
						tbl_query_times.qtime
					FROM
						tbl_question, tbl_research, tbl_query_times
					WHERE
						tbl_query_times.research_id = tbl_research.research_id AND
						tbl_question.query_id = tbl_query_times.query_id AND
						tbl_research.research_id = '$rid'
					ORDER BY
						tbl_query_times.qtime, tbl_question.number;";
						
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			return $native_result;				
		}
	}	// End of function getResearchQuestionInOrder0

	function getResearchQuestionsInOrder1($rid){	/* This query returns all the queries in the
													 * order as they appear in time after each other
													 * on the respondents phone
													 * when the data collection method is 1 (fixed interval) */																						
		$query =	"SELECT
						tbl_question.question,
						tbl_question.query_id,
						tbl_question.number,
						tbl_fixed_times.fixedtime
					FROM
						tbl_question, tbl_research, tbl_fixed_times
					WHERE
						tbl_fixed_times.research_id = tbl_research.research_id AND
						tbl_question.query_id = tbl_fixed_times.query_id AND
						tbl_research.research_id = '$rid'
					ORDER BY
						tbl_fixed_times.fixedtime, tbl_question.number;";
						
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			return $native_result;				
		}
	}	// End of function getResearchQuestionInOrder1

	function getResearchQuestionsInOrder2($rid){	// this query returns all the queries in the
													// order as they appear in time after each other
													// on the respondents phone
													// when the data collection method is 2 (event contingent)																				
		$query =	"SELECT
						tbl_question.question,
						tbl_question.query_id,
						tbl_question.number
					FROM
						tbl_question, tbl_research, tbl_query
					WHERE
						tbl_query.research_id = tbl_research.research_id AND
						tbl_question.query_id = tbl_query.query_id AND
						tbl_research.research_id = '$rid'
					ORDER BY
						tbl_question.number;";
						
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			return $native_result;				
		}
	}	// End of function getResearchQuestionInOrder2
	
	function getQueryAnswers($qrid){
		/*$query = "SELECT `a`.`research_id`, `a`.`query_id`, `a`.`question_id`, `a`.`answer_id`, `auth`.`username`, `a`.`time`,`q`.`number`, `q`.`question`,CASE `q`.`question_type`
			WHEN 1 THEN (SELECT `atext`.`text` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_text_answer` AS `atext` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `atext`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 2 THEN (SELECT `anum`.`num` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `anum`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 3 THEN (SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 4 THEN (SELECT `o`.`option` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum`, `tbl_option` AS `o` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`aid`.`question_id` AND `anum`.`answer_id`=`aid`.`answer_id` AND `o`.`question_id`=`q`.`question_id` AND `anum`.`num`=`o`.`option_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 5 THEN (SELECT `o`.`option` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum`, `tbl_option` AS `o` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`aid`.`question_id` AND `anum`.`answer_id`=`aid`.`answer_id` AND `o`.`question_id`=`q`.`question_id` AND `aid`.`answer_id`=`a`.`answer_id` AND `anum`.`num`=`o`.`option_id`)
			WHEN 7 THEN (SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 8 THEN (SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 9 THEN (SELECT `anum`.`num` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `anum`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			END AS `answer`, `q`.`question_type` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `a`,`tbl_auth` AS `auth` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `a`.`UID`=`auth`.`UID` AND `qr`.`query_id`='$qrid' ORDER BY `qr`.`query_id`,`q`.`number`,`a`.`time` ASC;";*/
		$query = "SELECT `a`.`research_id`, `a`.`query_id`, `a`.`question_id`, `a`.`answer_id`, `auth`.`username`, `a`.`time`,`q`.`number`, `q`.`question`,CASE `q`.`question_type`
			WHEN 1 THEN (SELECT `atext`.`text` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_text_answer` AS `atext` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `atext`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 2 THEN (SELECT `anum`.`num` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `anum`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 3 THEN (SELECT IF(ISNULL((SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`))>0,(SELECT `atext`.`text` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_text_answer` AS `atext` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `atext`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`),(SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)))
			WHEN 4 THEN (SELECT `o`.`option` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum`, `tbl_option` AS `o` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`aid`.`question_id` AND `anum`.`answer_id`=`aid`.`answer_id` AND `o`.`question_id`=`q`.`question_id` AND `anum`.`num`=`o`.`option_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 5 THEN (SELECT `o`.`option` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum`, `tbl_option` AS `o` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`aid`.`question_id` AND `anum`.`answer_id`=`aid`.`answer_id` AND `o`.`question_id`=`q`.`question_id` AND `aid`.`answer_id`=`a`.`answer_id` AND `anum`.`num`=`o`.`option_id`)
			WHEN 7 THEN (SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 8 THEN (SELECT `amedia`.`filename` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_media_answer` AS `amedia` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `amedia`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			WHEN 9 THEN (SELECT `anum`.`num` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `aid`,`tbl_num_answer` AS `anum` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `anum`.`answer_id`=`a`.`answer_id` AND `aid`.`answer_id`=`a`.`answer_id`)
			END AS `answer`, `q`.`question_type` FROM `tbl_research` AS `r`,`tbl_query` AS `qr`,`tbl_question` AS `q`,`tbl_answer` AS `a`,`tbl_auth` AS `auth` WHERE `r`.`research_id`=`qr`.`research_id` AND `qr`.`query_id`=`q`.`query_id` AND `q`.`question_id`=`a`.`question_id` AND `a`.`UID`=`auth`.`UID` AND `qr`.`query_id`='$qrid' ORDER BY `qr`.`query_id`,`q`.`number`,`a`.`time` ASC;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			return $native_result;				
		}
	}
	/*
	 * Used in functionality/displayResearch.php
	 * Check if visualize column
	 * @var $qid	query id
	 * @var $rid	research id
	 * @return	data in visualize column: NULL or !NULL
	 */
	function checkVisualize($qid, $rid){
		$query = "SELECT `visualize` FROM  `tbl_query` WHERE query_id= $qid AND research_id = $rid";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row = mysql_fetch_array($native_result);
			return $row[0];
		}
	}
}//end of class
?>
