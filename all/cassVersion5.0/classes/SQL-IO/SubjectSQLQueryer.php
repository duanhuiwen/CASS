<?php
class SubjectSQLQueryer extends SQLQueryer{

	function getSubject($id){					//Returns the name of the subject based on the ID given
		$query= "SELECT  * FROM `tbl_subject` WHERE `UID` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			return $native_result;				//Returns the subject based on the given user ID

		}
	}//end function
	
	function getInfo($id){
		$this->getSubject($id);
	}	
	
	function createTables2DB(){		
	}

	function getSubjectID($id){
		$query= "SELECT `subject_id` FROM `tbl_subject` WHERE `UID` =$id LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];						//Returns the subjects ID based on the user ID given
		}
	} //end of getSubjectID function
	
	function getSubjectByBT($bt_id){
		$query= "SELECT `UID`,`research_id` FROM `tbl_subject` WHERE `bt_id` ='$bt_id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			return $native_result;						//Returns the subjects ID based on the user ID given
		}
	} //end of getSubjectID function
	
	
	function getPrivar($id){
		$query= "SELECT * FROM `tbl_privar` WHERE `subject_id` =$id ORDER BY `number` ASC;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$privar = array();
			$num=mysql_numrows($native_result);
				for($i=0;$i<$num;$i++){
					$privar[$i]=array(
						'privar' => mysql_result($native_result,$i,"privateVar"),
						'number' => mysql_result($native_result,$i,"number"),
						);
					}
				return $privar;					//Returns the subjects private variables in an array
		}
	}//end of getPrivar function
	
	
	function getBt_id($id,$rid){
	$query= "SELECT `bt_id` FROM `tbl_subject` WHERE `UID` =$id AND `research_id` =$rid LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];						//Returns the bt ID based on the given subject ID
		}
	}//end of function getBt_id
	
	function setBt_id($id,$bt_id,$rid){
	$query= "UPDATE `tbl_subject` SET `bt_id`='$bt_id' WHERE `UID` ='$id' AND `research_id`='$rid' LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "InsertError";
		}else{
			return true;				
		}
	}//end of function setBt_id
	
	
	function isActive($uid){
		$query= "SELECT `tbl_research`.`research_id`, DATEDIFF(`tbl_research`.`startTime`,CURDATE()) AS Start,DATEDIFF(`tbl_research`.`endTime`,CURDATE()) AS End FROM `tbl_research`,`tbl_subject` WHERE `tbl_research`.`research_id` =`tbl_subject`.`research_id` AND `tbl_subject`.`UID`='$uid';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			return $native_result;
									//Returns is the subject active based on the given subject ID
		}
	}//end of function isActive
	
	function setPrivar($sid,$privar,$number){
		$query=("INSERT INTO `tbl_privar` (`var_id` ,`privateVar` ,`subject_id`, `number`) VALUES ('NULL','$privar', '$sid', '$number');"); 
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "InsertError";
		}else{
			return true;
		}
	}
	
	function rmPrivar($id){
		$query= "DELETE FROM `tbl_privar` WHERE `var_id` = $id;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data'); 
		if(MDB2::isError($result)){							//check weather query succeeded
			return false;
		}else{
			return true;
		}		
	}
	
	function getLastAnswer($uid,$rid){
		$query= "SELECT `time` FROM `tbl_answer` WHERE `UID` =$uid AND `research_id`=$rid ORDER BY `time` DESC;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			if(mysql_numrows($native_result)>0){
				$time = mysql_result($native_result,0,'time');
				$datenow = date('Y-m-d');
				$time = explode(" ",$time);
				$answer = array(
					"time" => $time[1],
					"day" => $time[0],
				);
				return $answer;	
			}else{
				return false;
			}						
		}
	}
	
	function getSurveyCount($uid,$rid){
		$datenow = date('Y-m-d');
		$query= "SELECT COUNT(*) FROM `tbl_answer` WHERE `UID` =$uid AND `research_id`=$rid AND `time` LIKE '%$datenow%';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];					
		}
	}
	/*
	 * This function is used when the data collection method is fixed interval. It 
	 * returns an array with the query_id of the next query, and the order number of that
	 * query. This function is called from the XMLGen.php to generate the query
	 * that is sent to the mobile client.
	 */
	function getNextQuery($sid,$rid){
		$query= "SELECT * FROM `tbl_track_fixed` WHERE `subject_id`='$sid' LIMIT 1;"; //why is rid not part of the clause???
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			if(mysql_numrows($native_result)>0){
				$lastanswer = mysql_result($native_result,0,'lastanswer');
				$last = explode(" ",$lastanswer); //splits the lastanswer into date last[0] and time last [1]
				$timesanswered = mysql_result($native_result,0,'timesanswered');
				$todays_date = date("Y-m-d"); //THIS MAY BE WRONG. see comment on next line
				$today = strtotime($todays_date); //strtotime returns seconds since January 1 1970 00:00:00 UTC, expects standard english date format
				$last_date = strtotime($last[0]); //what happens when the date does not include hours?
				if ($last_date == $today) { // This can cause problems if the mobile is not in the same timezone as the server. Or query happens just after midnight
					$r = new ResearchSQLQueryer();
					$times = $r->getFixedTimes($rid); // returns a MySQL table with the fixed interval queries
					if($timesanswered<mysql_numrows($times)){		//entäs, jos numrows on jotain hassua? Mit? jos vuorokausi vaihtuu			
						$interval = timeStampToSecs($r->getFixedInterval($rid)); //timeStapToSecs is in utilities.php
						$time = date("H:i:s");
						$time = timeStampToSecs($time); //seconds since midnight
						$lasttime = timeStampToSecs($last[1]);//last[1]=time
						$diff = ($time-$lasttime); //OK
						if($diff>=$interval){				
							$timesAnswered = $timesanswered+1; //lets get the query_id of the next query
							$query= "SELECT `query_id` FROM `tbl_fixed_times` WHERE `research_id`='$rid' AND `fixedtime`='$timesAnswered' LIMIT 1;"; //why limit?
							$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
							if(MDB2::isError($result)){
								return "SelectError";
							}else{
								$native_result = $result->getResource();
								$next = array(
									"query_id" => mysql_result($native_result,0,'query_id'),
									"fixedtime" => $timesAnswered,
								);
								return $next;
							}
						}else{
//							echo "wait for it. $diff vs. $interval. $time and $lasttime. int". $r->getFixedInterval($rid). " ".date("H:i:s");						
							return false;
						}
					}else{
						return false;
					}
				}
			}
			//What is this branch for, why it is not in the if -structure?
			//if $last_date != $today or tbl_track_fixed has no rows code below gets executed
			$r = new ResearchSQLQueryer();
			$first = $r->getFixedFirsttime($rid);
			$first = timeStampToSecs($first); //Pve 7.10.20210
			$time = date("H:i:s");
			$time = timeStampToSecs($time); //PVe 7.10.2010
			$diff = ($time-$first);
			if($diff>=0){
				$times = $r->getFixedTimes($rid);
				$next = array(
					"query_id" => mysql_result($times,0,'query_id'),
					"fixedtime" => mysql_result($times,0,'fixedtime'),
				);
				return $next;
			}else{
				return false;
			}
			
		}
	}
	
	function getUID($bt_id,$rid){
		$query= "SELECT `UID` FROM `tbl_subject` WHERE `bt_id` ='$bt_id' AND `research_id`='$rid';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];					
		}
	}
	
	function participatingIn($uid){
		$query= "SELECT `research_id` FROM `tbl_subject` WHERE `UID` ='$uid';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			return $native_result;					
		}
	}
	
	function checkBtId($bt_id,$rid){
		$query= "SELECT `research_id` FROM `tbl_subject` WHERE `bt_id` ='$bt_id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$num = mysql_numrows($native_result);
			if($num>0){
				for($i=0;$i<$num;$i++){
					$id = mysql_result($native_result,$i,'research_id');
					if($id!=$rid){
						/*$quer = "SELECT @a := DATEDIFF(`Re1`.`startTime`,`Re2`.`startTime`) FROM `tbl_research` AS Re1,`tbl_research` AS Re2 WHERE `Re1`.`research_id` ='$rid' AND `Re2`.`research_id` ='$id';" .
							"SELECT @r1 := DATEDIFF(`Re1`.`endTime`,`Re2`.`startTime`) AS diff FROM `tbl_research` AS Re1,`tbl_research` AS Re2 WHERE `Re1`.`research_id` ='$rid' AND `Re2`.`research_id` ='$id';" .
							"SELECT @r2 := DATEDIFF(`Re1`.`endTime`,`Re2`.`startTime`) AS diff FROM `tbl_research` AS Re1,`tbl_research` AS Re2 WHERE `Re1`.`research_id` ='$id' AND `Re2`.`research_id` ='$rid';" .
							"SELECT IF( @a <0, @r1 , @r2 ) AS diff;";*/
					 $quer = "SELECT IF( (SELECT DATEDIFF(`Re1`.`startTime`,`Re2`.`startTime`) FROM `tbl_research` AS Re1,`tbl_research` AS Re2 WHERE `Re1`.`research_id` ='$rid' AND `Re2`.`research_id` ='$id') <0, (SELECT DATEDIFF(`Re1`.`endTime`,`Re2`.`startTime`) AS diff FROM `tbl_research` AS Re1,`tbl_research` AS Re2 WHERE `Re1`.`research_id` ='$rid' AND `Re2`.`research_id` ='$id') , (SELECT DATEDIFF(`Re1`.`endTime`,`Re2`.`startTime`) AS diff FROM `tbl_research` AS Re1,`tbl_research` AS Re2 WHERE `Re1`.`research_id` ='$id' AND `Re2`.`research_id` ='$rid') ) AS diff";
						//$query= "SELECT DATEDIFF(`Re1`.`startTime`,`Re2`.`startTime`) AS startdiff,DATEDIFF(`Re1`.`endTime`,`Re2`.`startTime`) AS diff FROM `tbl_research` AS Re1,`tbl_research` AS Re2 WHERE `Re1`.`research_id` ='$rid' AND `Re2`.`research_id` ='$id';";
						$res = $this->mdb2->query($quer) or die('An unknown error occurred while checking the data');
						if(MDB2::isError($res)){
							return "SelectError";
						}else{
							$nresult = $res->getResource();
							$diff = mysql_result($nresult,0,'diff');						
							if($diff<0){
								$ok = true;
							}else{
								$ok = false;
								return false;
							}				
						}
					}else{
						$query2= "SELECT `research_id` FROM `tbl_subject` WHERE `bt_id` ='$bt_id' AND `research_id`='$rid';";
						$result2 = $this->mdb2->query($query2) or die('An unknown error occurred while checking the data');
						if(MDB2::isError($result2)){
							return "SelectError";
						}else{
							$nat_result = $result2->getResource();
							$n = mysql_numrows($nat_result);
							if($n>0){
								$ok = false;
								return false;
							}else{
								$ok = true;
							}
						}
					}
				}
				return $ok;		
			}else{
				return true;
			}					
		}
	}
	
	/*
	 * This function updates the tbl_track_fixed table with the time when the respondent
	 * answered last time the query and with the number how many times the query has been
	 * answered. MIKSI IHMEESS?EI VOI VAIN PÄIVITTÄÄ, KOSKA VASTAUS ON JO TALLETETTU KANTAAN, MIKSI if hässäkk?
	 */
	function setFixedAnswer($sid){
		$query= "SELECT `timesanswered`,`lastanswer` FROM `tbl_track_fixed` WHERE `subject_id` ='$sid' LIMIT 1;";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			if(mysql_numrows($native_result)>0){
				$last = mysql_result($native_result,0,'lastanswer');
				$last = explode(" ",$last); //$last[0] becomes YYYY-MM-DD
				$ans = mysql_result($native_result,0,'timesanswered');
				$todays_date = date("Y-m-d"); //$todays_date format is YYYY-MM-DD
				$today = strtotime($todays_date); //TÄM?PITÄÄ TARKISTAA, ehk?strtotime ei ymmärr?Y-m-d muotoa oikein vaan sotkee kuukauden ja päivän?
				//03-02-01  => 1. february 2003 (ISO) http://stackoverflow.com/questions/2207495/php-strtotime-looks-like-it-is-expecting-a-euro-format
				
				$last_date = strtotime($last[0]); 
				if ($last_date == $today) { 
					$timesAnswered = $ans+1;
				} else { 				
					 $timesAnswered = 1; //voi olla, ett?menee aina tähän haaraan???
				} 
				$query=("UPDATE `tbl_track_fixed` SET `timesanswered`='$timesAnswered' WHERE `subject_id` ='$sid';"); //miksi last answer datea ei päivitet?
																													//jossain muualla näin tehdään, koska taulussa on lastanswer					
			}else {
				$timesAnswered = 1;
				$query=("INSERT INTO `tbl_track_fixed` (`subject_id` ,`timesanswered` ) VALUES ('$sid','". $timesAnswered ."');");
			}									 
			$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
			if(MDB2::isError($result)){
				return "InsertError";
			}else{
				return true;
			}
		}
	}
	
}//end of class
?>