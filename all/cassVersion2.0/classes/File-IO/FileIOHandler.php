<?php
class FileIOHandler{
	var $files2db;
	var $writer;

	public function __construct(){
		include "../settings/dbsettings.php";		
 		//to database or not
 		$this->files2db = $files2db;
 		if($this->files2db == true){
 			$this->writer = new DBFileIO();
 		}elseif($this->files2db == false){
 			$this->writer = new DiskFileIO();
 		}else{
 			return "Error";
 		}
	}
	
	function MediaWrite($file,$type,$answer_id,$name){
		$this->writer->mediaWrite($file,$type,$answer_id,$name);	
	}
	
	function MediaRead($type,$answer_id){
		$tmp = $this->writer->mediaRead($type,$answer_id);
		return $tmp;
	}
	
	function getFiles2Db($answer_id=0){
		return $this->files2db;
	}
	
	function getQueryAnswers($query_id,$ext){
		if($this->writer->writeQueryAnswerTxt($query_id,$ext)){
			return true;
		}else{
			return false;
		}
	}
	
	function zipMediaFiles($research_id){
		$tmp = $this->writer->zipMediaFiles($research_id);
		return $tmp;
	}
	
	function writeQuery($query_id){
		//Query info
		$query = new Query($query_id);
		$queryName = $query->getName();
		$rid = $query->getOwner();
		//Research info
		$research = new Research($rid);
		$researchName = $research->getName();
		//start html
		$doc = "<h2>Query: $queryName</h2><p><b>Query ID: $query_id</b><br />";
		$doc .= "Research: $researchName , Research ID: $rid</p>";
		$doc .="<p><h3>Questions:</h3>";
		$doc .="<table border=\"0\">";
		$doc .="<tr><th>Question ID</th><th>Question</th><th>Question type</th><th>Category</th></tr>";
		//Questions
		$questions = $query->listChildren();
		for($i=0;$i<mysql_numrows($questions);$i++){
			$qid = mysql_result($questions,$i,"question_id");
			$question = mysql_result($questions,$i,"question");
			$qType = mysql_result($questions,$i,"question_type");
			$q = new Question(0,$qid);
			$qTypeTxt = $q->getQuestionTypeInText();
			$qNumber = mysql_result($questions,$i,"number");
			$qCat = mysql_result($questions,$i,"category");
			$doc .="<tr><td>$qid</td><td>$qNumber. $question</td><td>$qTypeTxt</td><td>$qCat</td></tr>";
			if($qType==4 || $qType==5 || $qType==9 || $qType==10){
				$opts = $q->getOptions();
				
				for($j=0;$j<count($opts);$j++){
					$option = $opts[$j]['option'];
					$number = $opts[$j]['number'];
					$superOf = $opts[$j]['super_of'];
					if($qType==4 || $qType==10){
						$doc .="<tr><td></td><td><i>option $number: $option</i></td><td></td><td></td></tr>";
					}elseif($qType==5){
						$doc .="<tr><td></td><td><i>option $number: $option</i></td><td>super of: $superOf</td><td></td></tr>";
					}elseif($qType==9){
						$doc .="<tr><td></td><td>  ";
						if($number==1){
							$doc .="<i>min.label: $option</i><br />";
						}elseif($number==2){
							$doc .="<i>min.value: $option</i><br />";
						}elseif($number==3){
							$doc .="<i>max.label: $option</i><br />";
						}elseif($number==4){
							$doc .="<i>max.value: $option</i><br />";
						}elseif($number==5){
							$doc .="<i>scale: $option</i>";
						}
						$doc .="</td><td></td><td></td></tr>";
					}
				}
			}
		}
		$doc .="</table>";
		//write file
		//$tmp_file = "../temp/query_".$query_id."_question.rtf";
		/*$file = fopen($tmp_file,"w");
		fwrite($file,$doc);
		fclose($file);*/
		return $doc;		
	}
	
	function writeUserInfo($rid){
		//Research info
		$research = new Research($rid);
		$name = $research->getName();
		$descr = $research->getDescr();
		$colmet = $research->getCollMethod();
		$method = $research->collMethod2String($colmet);
		$start = $research->getStartTime();
		$end = $research->getEndTime();
		$queries = $research->getQueriesPerDay();
		$children = $research->listChildren();
		$admins = $research->users->getAdmins();
		$researchers = $research->users->getResearchers();
		$subs = $research->users->getSubjects();
		
		//start html
			$doc = "<h1>$name</h1>";
			$doc .= "<p><b>Description:</b> $descr<br />";
			// Users in the research
			require_once("../common/includes.php"); //Class includes
			$doc .= "<h2>Users in research</h2>";
			$doc .= "<table><tr><td valign=\"top\">".genUserListForDoc("Administrators", $admins)."</td>";
			$doc .= "<td valign=\"top\">".genUserListForDoc("Researchers", $researchers)."</td>";
			$doc .= "<td valign=\"top\">".genUserListForDoc("Subjects", $subs,$rid)."</td></tr></table>";
		return $doc;
	}
	
	function writeResearch($rid){
		//Research info
		$research = new Research($rid);
		$name = $research->getName();
		$descr = $research->getDescr();
		$colmet = $research->getCollMethod();
		$method = $research->collMethod2String($colmet);
		$start = $research->getStartTime();
		$end = $research->getEndTime();
		$queries = $research->getQueriesPerDay();
		$children = $research->listChildren();
		$admins = $research->users->getAdmins();
		$researchers = $research->users->getResearchers();
		$subs = $research->users->getSubjects();
		
		//start html
			$doc = "<h1>$name</h1>";
			$doc .= "<p><b>Description:</b> $descr<br />";
			$doc .= "Starts: $start<br />";
			$doc .= "Ends: $end<br /></p>";
			$doc .= "<p><b>Data collection method:</b> $method<br /></p>";
			if($colmet!=2){
				$doc .= "$queries queries are sent per day<br />";
			}
			if($colmet==0){
				$doc .= "<br /><b>Query times:</b><br />";
				$qtimes = $research->getQueryTimes();
				$qnum = mysql_numrows($qtimes);
				if($qnum>0){
					for($i=0;$i<$qnum;$i++){
						$qtime = mysql_result($qtimes,$i,"qtime");
						$queryid = mysql_result($qtimes,$i,"query_id");
						
						$queryid_array[$i] = $queryid; // creating an array to store the order of the queries
						
						$doc .= "$qtime";
						if($queryid!=0){
							$query = new Query($queryid);
							$qname = $query->getName();
							$doc .= " assigned for query: $qname";
						}
						$doc .= "<br />";
					}
				}
			}elseif($colmet==1){
				$doc .= "<br /><b>Fixed time:</b><br />";
				$firsttime = $research->getFixedFirsttime();
				$interval = $research->getFixedInterval();
					
				$qFixedTimes = $research->getFixedTimes(); // get the fixed times to create an array from them
				for($i=0;$i<mysql_numrows($qFixedTimes);$i++){
					$queryid = mysql_result($qFixedTimes,$i,"query_id");
					$queryid_array[$i] = $queryid; // creating an array to store the order of the queries
				}
					
				$doc .="First query set at: ".$firsttime."<br />Interval between queries: ".$interval;
			}elseif($colmet==2){
				for($j=0;$j<mysql_numrows($children);$j++){
					$queryid = mysql_result($children,$j,'query_id');
					$queryid_array[$j] = $queryid; // creating an array to store the order of the queries
				}
			}
			
			// Queries in the research
			/* 	$doc .= "<h2>Queries in research</h2>";
			*	for($j=0;$j<mysql_numrows($children);$j++){
			*	$query_id = mysql_result($children,$j,'query_id');
			*	$doc .= $this->writeQuery($query_id);
			*	}
			*/
			
			// Queries in the research
			$doc .= "<h2>Queries in research</h2>";
			for($j=0;$j<count($queryid_array);$j++){
				if ($queryid_array[$j] == 0){
					$doc .= "<h3>#".($j+1)."</h3><h2>Query: Query has not yet been assigned.</h2>";
				}else{
					$doc .= "<h3>#".($j+1)."</h3>".$this->writeQuery($queryid_array[$j]);
				}	
			}

			// Users in the research
			require_once("../common/includes.php"); //Class includes
			$doc .= "<h2>Users in research</h2>";
			$doc .= "<table><tr><td valign=\"top\">".genUserListForDoc("Administrators", $admins)."</td>";
			$doc .= "<td valign=\"top\">".genUserListForDoc("Researchers", $researchers)."</td>";
			$doc .= "<td valign=\"top\">".genUserListForDoc("Subjects", $subs,$rid)."</td></tr></table>";
		return $doc;
	}
	
	function getResearchAnswers($rid){
		if($this->writer->writeResearchAnswerTxt($rid)){
			return true;
		}else{
			return false;
		}
	}
	
	function getResearchAnswers2($rid){	
		if($this->writer->writeResearchAnswerTxt2($rid)){
			return true;
		}else{
			return false;
		}
	}	
	
	function getResearchAnswers3($rid){	
		if($this->writer->writeResearchAnswerTxt3($rid)){
			return true;
		}else{
			return false;
		}
	}	
	
	function writeFile($filepath,$data,$method=0){
		//write file
		if($method==0){
			$file = fopen($filepath,"w");
		}else{
			$file = fopen($filepath,$method);
		}
		fwrite($file,$data);
		fclose($file);
		return $filepath;
	}
	
}
?>