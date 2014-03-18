<?php
/*
 * If the $files2db is set to true then these functions are used to reach the data of the
 * research. All the media files sent from the mobile client will be saved into database.
 * The methods belows are tested and work. Might be that some of the method should be
 * implemented in the DiskFileIO.php.
 */

class DBFileIO extends FileIO{
	private $file;
	private $type;
	private $answer_id;
		
	function __construct(){
		parent::__construct();		
	}

	function mediaWrite($nFile,$nType,$nAnswer_id,$name){
		$this->file = $nFile;
		$this->type = $nType;
		$this->answer_id = $nAnswer_id;
		
		if($this->file != null) {
			$FSize = strlen($this->file);
			/*$f = fopen($this->file, "rb");
			$bin_data = fread($f, $FSize);
			$mysqlData = addslashes($bin_data);*/
			$mysqlData = addslashes($this->file);	
			//$query = ("INSERT INTO `tbl_media_answer` (`media_id`,`answer_id`,`media`) VALUES (null,'$this->answer_id','$mysqlData');");
			$query = $query = ("UPDATE `tbl_media_answer` SET `media`='$mysqlData', `filename`='$name' WHERE `answer_id`='$this->answer_id';");
			$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($result)){
				die($result->getMessage());
				return "InsertError"; 			//Return error if error resulted
			}else{
				$query=("SELECT `media_id` FROM `tbl_media_answer` WHERE `answer_id` = '$this->answer_id' LIMIT 1;");
				$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
				if(MDB2::isError($result)){
					die($result->getMessage());
					return "SelectError";
				}else{
					$native_result = $result->getResource();
					$row=Mysql_Fetch_Row($native_result);
					return $row[0];				//Returns the newly created media ID number in the database.
				}
			}
		}else {			
			return false;
		} 
		
	}//end of mediawrite
	
	function mediaRead($nType,$nAnswer_id){
		$this->type = $nType;
		$this->answer_id = $nAnswer_id;
		$query=("SELECT `media` FROM `tbl_media_answer` WHERE `answer_id`='$this->answer_id';");
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result2)){
			throw new Exception("SelectError");
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			//$a = removeslashes($row[0]);
			return $row[0];
		}
	}//end of function mediaRead
	
	/*
	 * This function writes the answers of the query in a txt or xls(Excel) file depending
	 * on the $ext.
	 */
	function writeQueryAnswerTxt($query_id,$ext){
		//filename
		$filename = "../temp/Query_".$query_id."_Answers.$ext";
		//File structure
		$txtFileHeader = "Research ID\tQuery ID\tQuestion ID\tAnswer ID\tUsername\tTime\tQuestion\tAnswer\tType\n";

		if(!$file = fopen($filename,'a')){
			return false;
		}
		if(fwrite($file,$txtFileHeader)===FALSE){
			return false;
		}
		
		$sql = new AnswerSQLQueryer();
		$answers  = $sql->getQueryAnswers($query_id);
		$num = mysql_numrows($answers);
		for($i=0;$i<$num;$i++){
			$rid = mysql_result($answers,$i,'research_id');
			$qrid = mysql_result($answers,$i,'query_id');
			$qid = mysql_result($answers,$i,'question_id');
			$aid = mysql_result($answers,$i,'answer_id');
			$usr = mysql_result($answers,$i,'username');
			$time = mysql_result($answers,$i,'time');
			$nr = mysql_result($answers,$i,'number');
			$question = mysql_result($answers,$i,'question');
			$answer = mysql_result($answers,$i,'answer');
			$type = mysql_result($answers,$i,'question_type');
			if($type==3 || $type==7 || $type==8){
				if($type==3 && $sql->getFileName($aid)==NULL){ //Checking if sound answer is set to text
	
				}else{
					$path = full_url();
					$replace = "functionality";
					$path = str_replace($replace,"content",$path);
					$path = pathinfo($path);
					$path =  $path['dirname'];
					$answer = $path."/showpic.php?picID=".$aid;
				}
			}
			
			$txtFile = "$rid\t$qrid\t$qid\t$aid\t$usr\t$time\t$nr $question\t".utf8_decode($answer)."\t$type\n";
			if(fwrite($file,$txtFile)===FALSE){
					return false;
			}
		}		
		return true;		
	}//end of writeQueryAnswerTxt
	
	/*
	 * This function draws an excel table in such way that the each row represents a filled
	 * query. After the first columns (research id, query id, time of the answer and the username),
	 * in each column there are answers for the question that will be shown in the first line.
	 * The questions in the system arrives in alphabetical order from the databse in order that
	 * we could put the answers for the same question in the same column. At the moment the only
	 * way to make a comparison on two question is to compare the strings.
	 * The order of the questions could be different than alphabetical but it would complicate
	 * the sorting, and because we do not have any other well defined order to work with, makes no
	 * sense.
	 */
	function writeResearchAnswerTxt($research_id){
		// to run forever NOTE: QUICK FIX, doesn't solve the DB Problem
		ignore_user_abort();
		set_time_limit(0);
		//filename
		$filename = "../temp/Research_".$research_id."_Answers.xls";
		/*
		 * Getting the questions in the research.
		 */
		$sql2 = new AnswerSQLQueryer();	// creating new SQL object for the answers
		$questions = $sql2->getResearchQuestions($research_id);	// gets the questions
		$numbers = mysql_numrows($questions);	// number of the questions
		/* Creating a question array for storing the questions in a certain order.
		 * The getResearchQuestions method gives back all the questions in alphabetical order.
		 * If the end of the array is not the same as the next trimmed question then
		 * the value is pushed into the array.
		 */				
		$question_array[0] = trim(mysql_result($questions,0,'question'));
		for($j=1;$j<$numbers;$j++){
			if (end($question_array) != trim(mysql_result($questions,$j,'question')) ){
				$question_to_push = trim(mysql_result($questions,$j,'question'));
				array_push($question_array, $question_to_push);
			}
		}
	
		// Write the column headings
		// File structure
		$txtFileHeader = "Research ID\tQuery ID\tUsername\tTime";
		/*
		 * Into the header of the Excel file the whole content of the question array is printed
		 * separated by tabulatures (/t).
		 */
		foreach($question_array as $value){
			$txtFileHeader .= "\t" . $value; 
		}
		// Print line break.				
		$txtFileHeader .= "\n";
		// File is opened.
		if(!$file = fopen($filename,'a')){
			return false;
		}
		// Writes the file header into the file.
		if(fwrite($file,$txtFileHeader)===FALSE){
			return false;
		}
		/*
		 * Getting the actual answers and starting to handle them.
		 */		
		$sql = new AnswerSQLQueryer();	// creating an SQL object for the answers
		$answers  = $sql->getResearchAnswers($research_id);	// fill up the SQL object
		$num = mysql_numrows($answers);	// number of rows in the SQL table
		for($i=0;$i<$num;$i++){	// Runs through every row in the SQL table.
			$rid = mysql_result($answers,$i,'research_id');
			$qrid = mysql_result($answers,$i,'query_id');
			$qid = mysql_result($answers,$i,'question_id');
			$aid = mysql_result($answers,$i,'answer_id');
			$usr = mysql_result($answers,$i,'username');
			$time = mysql_result($answers,$i,'time');
			$nr = mysql_result($answers,$i,'number');
			$question = mysql_result($answers,$i,'question');
			$question = trim($question);	// remove whitespaces from the question
			$answer = mysql_result($answers,$i,'answer');
			$type = mysql_result($answers,$i,'question_type');
			//$typetxt = $que->getQuestionTypeInText();
			if($type==3 || $type==7 || $type==8){
				if($type==3 && $sql->getFileName($aid)==NULL){ //Checking if sound answer is set to text
					//Do nothing
				}else{
					$path = full_url();
					$replace = "functionality";
					$path = str_replace($replace,"content",$path);
					$path = pathinfo($path);
					$path =  $path['dirname'];
					$answer = $path."/showpic.php?picID=".$aid;
				}
			}
			/*
			 * Sorting the answers. Under every question columns in the Excel table comes
			 * the corresponding answer.
			 * In the first loop the variables are set up that will help the sorting.
			 * The $prevUsr, $prevQrid, $prevTime variables store the values of $usr, $qrid,
			 * $time. They are stroring the username, the question id and the time of the
			 * answer.
			 */
			if ($i==0) {	
				$prevUsr = $usr;
				$prevQrid = $qrid;
				$prevTime = $time;
				
				$txtRow = "$rid\t$qrid\t$usr\t$time"; // Writes the research id, the query id, username and the time of the answer 
				for ($c=0;$c < count($question_array);$c++){	// Checks through the question array, 
					$txtRow .= "\t";							// in every loop it adds a new tabulature.
					if($question == $question_array[$c]){		// If the question that came from the answers query is the same as the question in the question array
						$txtRow .= utf8_decode($answer);		// it prints the answer.
						
						if ($i == ($num -1 )){ // added 25.5.2009	// If $i equals the number of the rows of answers minus 1 
							$txtFile = $txtRow."\n";				// then writes the data to file.
							if(fwrite($file,$txtFile)===FALSE){
								return false;
							}
						}												
						$c++;
						break;	// Jumps out from the 'for' loop and give one to the $c counter  
					}
				}
			/*
			 * If the username, the query id and the time of the answer has not changed
			 * since the last loop, it will execute a similar comparison as above:
			 * add the necessary tabs, puts the answer in the cell.
			 */				
			} elseif ($usr == $prevUsr && $qrid == $prevQrid && $time == $prevTime) {
				// Does while loop to add tabs if neccessary in front of the actual answer.
				for ($c;$c < count($question_array);$c++){	// Checks through the question array,
					$txtRow .= "\t";						// in every loop it adds a new tabulature.
					if($question == $question_array[$c]){	// If the question that came from the answers query is the same as the question in the question array
						$txtRow .= utf8_decode($answer);	// it prints the answer.
						
						if ($i == ($num -1 )){ // added 25.5.2009	// If $i equals the number of the rows of answers minus 1 
							$txtFile = $txtRow."\n";				// then writes the data to file.
							if(fwrite($file,$txtFile)===FALSE){
								return false;
							}
						}						
						$c++;
						break;	// Jump out from the 'for' loop and give one to the $c counter
					}
				}
			/*
			 * If the username, the query id or the time of the answer has changed
			 * since the last loop, it will execute a similar comparison as above:
			 * add the necessary tabs, puts the answer in the cell.
			 */
			} else {
				// End of record. Puts the data collected in $txtRow into $txtFile, ready for writing.
				$txtFile = $txtRow."\n";
				// Reinitalization of the $txtRow and adds the research id, query id, username and time of answering information 
				$txtRow = "$rid\t$qrid\t$usr\t$time";			
				for ($c=0;$c < count($question_array);$c++){	// Reinitializes $c. Checks through the question array.
					$txtRow .= "\t";							// in every loop it adds a new tabulature.
					if($question == $question_array[$c]){		// If the question that came from the answers query is the same as the question in the question array
						$txtRow .= utf8_decode($answer);		// it prints the answer.
						
						if ($i == ($num -1 )){			// If $i equals the number of the rows of answers minus 1
							$txtFile .= $txtRow."\n";	// then writes the data to file.
						}
						$c++;
						break;
					}
				}
				
				$prevUsr = $usr;	// The new username,
				$prevQrid = $qrid;	// query id,
				$prevTime = $time;	// and answering time will be the base of the comparison.
				
				if(fwrite($file,$txtFile)===FALSE){	// Writing the $txtFile into file.
					return false;
				}
			}
		}
		return true;
	}//end of writeAnswerTxt
	
	/*
	 * This function draws an excel table in such way that the each row represents an
	 * answer given by the respondents. The columns in order are the followings:
	 * research id, query id, question id, answer id, username, time of the answer,
	 * question, answer and the type of the question.
	 * As the data in this form can easily exceed the row capacity of the Excel,
	 * it might be problematic to use it. But as this function does not use any comparison
	 * this method proved to be the safest method to get back the data.
	 */	
	function writeResearchAnswerTxt2($research_id){
		// to run forever NOTE: QUICK FIX, doesn't solve the DB Problem
		ignore_user_abort();
		set_time_limit(0);
		//filename
		$filename = "../temp/Research_".$research_id."_Answers_v2.xls";
		
		// Write the column headings
		// File structure, printing the header of the Excel file.
		$txtFileHeader = "Research ID\tQuery ID\tQuestion ID\tAnswerID\tUsername\tTime\tQuestion\tAnswer\tType\n";
		// File creation.
		if(!$file = fopen($filename,'a')){
			return false;
		}
		// Writing the data into file.
		if(fwrite($file,$txtFileHeader)===FALSE){
			return false;
		}
		
		// Getting the actual answers and starting to handle them.
		$sql = new AnswerSQLQueryer();
		$answers  = $sql->getResearchAnswers($research_id); // Getting the answers.	
		$num = mysql_numrows($answers);	// Number of the rows in the MySQL table
		for($i=0;$i<$num;$i++){
			$rid = mysql_result($answers,$i,'research_id');
			$qrid = mysql_result($answers,$i,'query_id');
			$qid = mysql_result($answers,$i,'question_id');
			$aid = mysql_result($answers,$i,'answer_id');
			$usr = mysql_result($answers,$i,'username');
			$time = mysql_result($answers,$i,'time');
			$nr = mysql_result($answers,$i,'number');
			$question = mysql_result($answers,$i,'question');
			$question = trim($question);	// remove whitespaces from the question
			$answer = mysql_result($answers,$i,'answer');
			$type = mysql_result($answers,$i,'question_type');
			if($type==3 || $type==7 || $type==8){
				if($type==3 && $sql->getFileName($aid)==NULL){ //Checking if sound answer is set to text
					//Do nothing
				}else{// Changes the text address to give a valid link to show the media content
					$path = full_url();
					$replace = "functionality";
					$path = str_replace($replace,"content",$path);
					$path = pathinfo($path);
					$path =  $path['dirname'];
					$answer = $path."/showpic.php?picID=".$aid;
				}
			}elseif($type==10){ // For the Multiple choice-multiple answer questions adds to the option number the text versions too
				$opt = new AnswerSQLQueryer();
				$options = explode(",", $answer);
				foreach($options as $value){
					if($value){
						$answer .= " ".$opt->getOptionText($value).",";
					}   
				}
			}
			
			// Writing the answer in the given format to disk.
			$txtFile = "$rid\t$qrid\t$qid\t$aid\t$usr\t$time\t$nr $question\t".utf8_decode($answer)."\t$type\n";
			if(fwrite($file,$txtFile)===FALSE){
				return false;
			}
		}
		return true;
	}//end of writeAnswerTxt2
	
	/*
	 * This function draws an excel table in such way that the each row represents an
	 * answer given by the respondents. After the first columns (research id, query id,
	 * time of the answer and the username), in each column there are answers for the question
	 * that will be shown in the first row.
	 * The questions arrives in the order as the queries follow each other. Here the problem
	 * can be that the number of the columns exceed the available row number in the Excel sheet.
	 */		
	function writeResearchAnswerTxt3($research_id){
		// to run forever NOTE: QUICK FIX, doesn't solve the DB Problem
		ignore_user_abort();
		set_time_limit(0);
		// filename
		$filename = "../temp/Research_".$research_id."_Answers_v3.xls";
		
		// Getting the questions in the research.		
		$r = new Research($research_id); // creating a new Research object
		$method = $r->getCollMethod();	// to get back the research collection method
		
												// According to the coll method it runs a different database query
		if ($method == 0){						// to get the questions in order
			$sql3 = new AnswerSQLQueryer();
			$questions = $sql3->getResearchQuestionsInOrder0($research_id);
			$numbers = mysql_numrows($questions);
		}elseif ($method == 1){
			$sql3 = new AnswerSQLQueryer();
			$questions = $sql3->getResearchQuestionsInOrder1($research_id);
			$numbers = mysql_numrows($questions);
		}elseif ($method == 2){
			$sql3 = new AnswerSQLQueryer();
			$questions = $sql3->getResearchQuestionsInOrder2($research_id);
			$numbers = mysql_numrows($questions);
		}
		
		/* Creating a question array for storing the questions in a certain order.
		 * The getResearchQuestions method gives back all the questions in alphabetical order.
		 * If the end of the array is not the same as the next trimmed question then
		 * the value is pushed into the array.
		 */
		$question_array[0] = mysql_result($questions,0,'question');
		for($j=1;$j<$numbers;$j++){
			$question_to_push = mysql_result($questions,$j,'question');
			array_push($question_array, $question_to_push);
		}
	
		// Write the column headings
		// File structure
		$txtFileHeader = "Research ID\tQuery ID\tUsername\tTime$method";
		
		/*
		 * Into the header of the Excel file the whole content of the question array is printed
		 * separated by tabulatures (/t).
		 */		
		foreach($question_array as $value){
			$txtFileHeader .= "\t" . $value; 
		}
		// Prints line break.		
		$txtFileHeader .= "\n";
		// Opens the file for writing.
		if(!$file = fopen($filename,'a')){
			return false;
		}
		if(fwrite($file,$txtFileHeader)===FALSE){
			return false;
		}
				
		/*
		 * Getting the actual answers and starting to handle them.
		 */
		$sql4 = new AnswerSQLQueryer();
		$answers  = $sql4->getResearchAnswers3($research_id);
		$num = mysql_numrows($answers);
		for($i=0;$i<$num;$i++){
			$rid = mysql_result($answers,$i,'research_id');
			$qrid = mysql_result($answers,$i,'query_id');
			$qid = mysql_result($answers,$i,'question_id');
			$aid = mysql_result($answers,$i,'answer_id');
			$usr = mysql_result($answers,$i,'username');
			$time = mysql_result($answers,$i,'time');
			$nr = mysql_result($answers,$i,'number');
			$question = mysql_result($answers,$i,'question');
			$answer = mysql_result($answers,$i,'answer');
			$type = mysql_result($answers,$i,'question_type');
			//$typetxt = $que->getQuestionTypeInText();
			if($type==3 || $type==7 || $type==8){
				if($type==3 && $sql4->getFileName($aid)==NULL){ //Checking if sound answer is set to text
					//Do nothing
				}else{
					$path = full_url();
					$replace = "functionality";
					$path = str_replace($replace,"content",$path);
					$path = pathinfo($path);
					$path =  $path['dirname'];
					$answer = $path."/showpic.php?picID=".$aid;
				}
			}
			/*
			 * Sorting the answers. Under every question columns in the Excel table comes
			 * the corresponding answer.
			 * In the first loop the variables are set up that will help the sorting.
			 * The $prevUsr, $prevQrid, $prevTime variables store the values of $usr, $qrid,
			 * $time. They are stroring the username, the question id and the time of the
			 * answer.
			 */
			if ($i==0) {
				$prevUsr = $usr;
				$prevQrid = $qrid;
				$prevTime = $time;
				
				//
				$txtRow = "$rid\t$qrid\t$usr\t$time";	// Writes the research id, the query id, username and the time of the answer
				for ($c=0;$c < count($question_array);$c++){	// Checks through the question array,
					$txtRow .= "\t";							// in every loop it adds a new tabulature.
					if($question == $question_array[$c]){		// If the question that came from the answers query is the same as the question in the question array
						$txtRow .= utf8_decode($answer);		// it prints the answer.
						
						if ($i == ($num-1) ){ // added 25.5.2009	// If $i equals the number of the rows of answers minus 1 
							$txtFile = $txtRow."\n";				// then writes the data to file.
							if(fwrite($file,$txtFile)===FALSE){
								return false;
							}
						}
						
						$c++;
						break;	// Jumps out from the 'for' loop and give one to the $c counter
					}
				}
				
			/*
			 * If the username, the query id and the time of the answer has not changed
			 * since the last loop, it will execute a similar comparison as above:
			 * add the necessary tabs, puts the answer in the cell.
			 */				
			} elseif ($usr == $prevUsr && $qrid == $prevQrid && $time == $prevTime) {
				
				// Does while loop to add tabs if neccessary in front of the actual answer
				for ($c;$c < count($question_array);$c++){
					$txtRow .= "\t";
					if($question == $question_array[$c]){
						$txtRow .= utf8_decode($answer);
												
						if ($i == ($num-1) ){ // added 25.5.2009 
							$txtFile = $txtRow."\n";
							if(fwrite($file,$txtFile)===FALSE){
								return false;
							}
						}
												
						$c++;
						break;
					}
				}
			/*
			 * If the username, the query id or the time of the answer has changed
			 * since the last loop, it will execute a similar comparison as above:
			 * add the necessary tabs, puts the answer in the cell.
			 */	
			} else {
				// End of record. Puts the data collected in $txtRow into $txtFile, ready for writing.
				$txtFile = $txtRow."\n";
				// Reinitalization of the $txtRow and adds the research id, query id, username and time of answering information
				$txtRow = "$rid\t$qrid\t$usr\t$time";
				for ($c=0;$c < count($question_array);$c++){	// Reinitializes $c. Checks through the question array.
					$txtRow .= "\t";							// in every loop it adds a new tabulature.
					if($question == $question_array[$c]){		// If the question that came from the answers query is the same as the question in the question array
						$txtRow .= utf8_decode($answer);		// it prints the answer.
						if ($i == ($num -1) ){
							$txtFile .= $txtRow."\n";
						}
						$c++;
						break;
					}
				}
				
				$prevUsr = $usr;	// The new username,
				$prevQrid = $qrid;	// query id,
				$prevTime = $time;	// and answering time will be the base of the comparison.
				
				if(fwrite($file,$txtFile)===FALSE){
					return false;
				}
			}			
		}		
		return true;		
	}//end of writeAnswerTxt3

	/*
	 * This function gets all the media files in the research and adds them into an zip
	 * archive.
	 */
	function zipMediaFiles($research_id){
		// to run forever NOTE: QUICK FIX, doesn't solve the DB Problem
		ignore_user_abort();
		set_time_limit(0);
		
		$zip = new ZipArchive;
		$filename = "../MediaFiles/Research_$research_id/Research_$research_id.zip";
		//writing data to a file
		$dir = "../MediaFiles/Research_$research_id";
		if(!is_dir($dir)){
			mkdir($dir,0777);
		}
		if(is_dir($dir)){
			if($zip->open($filename,ZIPARCHIVE::CREATE)!== TRUE){
				return "Error";
			}
			//Get files from db and save them to a temp folder
			$r = new Research($research_id);
			$queries = $r->listChildren();
			$qnum = mysql_numrows($queries);
			if($qnum>0){
				for($i=0;$i<$qnum;$i++){
					$query_id = mysql_result($queries,$i,'query_id');
					$q = new Query($query_id);
					$questions = $q->listChildren();
					$qsnum = mysql_numrows($questions);
					if($qsnum>0){
						for($j=0;$j<$qsnum;$j++){
							$question_id = mysql_result($questions,$j,'question_id');
							$type = mysql_result($questions,$j,'question_type');
							if($type==3 || $type==7 || $type==8){
								$qs = new Question(0,$question_id);
								$answers = $qs->listChildren();
								$anum = count($answers);
								if($anum>0){
									for($k=0;$k<$anum;$k++){
										$data = $this->mediaRead($type,$answers[$k]['answer_id']);
										$a = new Answer($answers[$k]['answer_id']);
										$name = $a->getMediaFileName();
										//$filepath = $dir."/$name";
										/*$file = @fopen($filepath,"w");
										@fwrite($file,$data);
										@fclose($file);		
										$zipEntryName = $name;*/
										
					
										if($this->addFileToZip($filename,$data,$name)){
		   									//chdir($dir);
											//chmod($dir, 0777);
	    									//unlink($name);
		   								}else{
		   									echo "zipError $k<br />";
		   								}
									}
								}
							}
						}
					}
				}
			}		
		return $filename;
		}
	}
	
	function addFileToZip($zipfile,$data,$zipEntryName){
		$zip = new ZipArchive;
		$res = $zip->open($zipfile, ZIPARCHIVE::CREATE);
		if($res === TRUE){
			/*$contents = @file_get_contents($path);
			if($contents === false){
				return false;
			}
    		$zippi = $zip->addFromString($zipEntryName,$contents);
    		$zip->close();
   			return true;*/
			//$data = addslashes();
			$tmp = $zip->addFromString($zipEntryName,$data);
			$zip->close();
			return $tmp;
		}else{
    		return false;
		}
		
	}
	
	
}//end of class
?>