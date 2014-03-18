<?php
/*
 * If the $files2db is set to false then these functions are used to reach the data of the
 * research. All the media files sent from the mobile client will be saved onto disk. Only
 * the path is saved in the databse. (not tested @1.7.2009)
 */

class DiskFileIO extends FileIO{

	
public function __construct(){
	parent::__construct();
}

public function mediaWrite($file, $type,$answer_id,$name){
	if(strlen($file)==0){
		return false;
	}else{
		$media=$this->path;
		//Folder for each media file
		$a = new Answer($answer_id);
		$media.="Research_".$a->getResearchID()."/";
		if(!is_dir($media)){
			mkdir($media,0755);
			//chmod($media,"root");
		}
		$media.= "Query_".$a->getQueryID()."/";
		if(!is_dir($media)){
			mkdir($media,0755);
		}		
		//Checking what type the file is
		if($type==7){
			$media.=$this->img;			
		}elseif($type==3){
			$media.=$this->voice;
		}elseif($type==8){
			$media .= $this->video;
		}else{
			return false;
		}		
		if(!is_dir($media)){
			mkdir($media,0755);
		}
		$media.= $name;
		$FSize = strlen($file);
		$f = fopen($media,'w');
		fwrite($f,$file,$FSize);
		fclose($f);
		//File path to database
		//$query="INSERT INTO `tbl_media_answer`(`media_id`,`answer_id`,`media`,`filepath`,`filename`) VALUES (NULL,'$answer_id',NULL,\"$media\",'$name');";
		$query="UPDATE `tbl_media_answer` SET `filepath`=\"$media\" WHERE `answer_id`='$answer_id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			throw new Exception("InsertError"); 								//Return error if error resulted
		}else{
			return true;
		}					
	}	
}


public function mediaWrite1($temp_file_path,$type,$answer_id,$name){
	echo "<br>inside mediawrite1 function<br>";
	
	if(strlen($temp_file_path)==null){
		return false;
	}else{
		$media=$this->path;
		//Folder for each media file
		$a = new Answer($answer_id);
		$media.="Research_".$a->getResearchID()."/";
		if(!is_dir($media)){
			mkdir($media,0755);
			//chmod($media,"root");
		}
		$media.= "Query_".$a->getQueryID()."/";
		if(!is_dir($media)){
			mkdir($media,0755);
		}
		//Checking what type the file is
		if($type==7){
			$media.=$this->img;
		}elseif($type==3){
			$media.=$this->voice;
		}elseif($type==8){
			$media .= $this->video;
		}else{
			return false;
		}
		if(!is_dir($media)){
			mkdir($media,0755);
		}
		$media.= $name;
		//$FSize = filesize($temp_file_path);
		$f = fopen($media,'w');
		//move the file from temp location on server to MediaFiles dir
		
		if(move_uploaded_file($temp_file_path, $media)){
			echo $temp_file_path ."in side function";
			echo "<br>moved file<br>";
		}
		//fwrite($f,$file,$FSize);
		//fclose($f);
		//File path to database
		//$query="INSERT INTO `tbl_media_answer`(`media_id`,`answer_id`,`media`,`filepath`,`filename`) VALUES (NULL,'$answer_id',NULL,\"$media\",'$name');";
		$query="UPDATE `tbl_media_answer` SET `filepath`=\"$media\" WHERE `answer_id`='$answer_id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			throw new Exception("InsertError"); 								//Return error if error resulted
		}else{
			return true;
		}
	}
}




public function MediaRead($type,$answer_id){
	if($type != null){
	switch($type){
	case 3: 		
		$query="SELECT `filepath` FROM `tbl_media_answer` WHERE `answer_id`='$answer_id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($result)){
				throw new Exception("SelectError"); 								//Return error if error resulted
			}else{
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return "<embed src=\"$row[0]\" autostart=\"true\"></embed>";
			}
		break;		
	case 7:
		$query="SELECT `filepath` FROM `tbl_media_answer` WHERE `answer_id`='$answer_id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($result)){
				throw new Exception("SelectError"); 								//Return error if error resulted
			}else{
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return "<img src=\"$row[0]\" width=\"400\" height=\"300\" alt=\"Answer: $answer_id\"></img>";
			}
		break;
	case 8:
		$query="SELECT `filepath` FROM `tbl_media_answer` WHERE `answer_id`='$answer_id';";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
			if(MDB2::isError($result)){
				throw new Exception("SelectError"); 								//Return error if error resulted
			}else{
				$native_result = $result->getResource();
				$row=Mysql_Fetch_Row($native_result);
				return "<embed src=\"$row[0]\" autostart=\"true\"></embed>";
			}
		break;
	}
	}else{
		return false;
	}
}//end of function


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
			$path = full_url();
			$replace = "functionality";
			$path = str_replace($replace,"content",$path);
			$path = pathinfo($path);
			$path =  $path['dirname'];
			$answer = $path."/showpic.php?picID=".$aid;
		}
		$txtFile = "$rid\t$qrid\t$qid\t$aid\t$usr\t$time\t$nr $question\t".utf8_decode($answer)."\t$type\n";
		if(fwrite($file,$txtFile)===FALSE){
				return false;
		}
	}
	return true;
		
}//end of writeAnswerTxt

	function writeResearchAnswerTxt($research_id){
	//filename
		$filename = "../temp/Research_".$research_id."_Answers.xls";
		//File structure
		$txtFileHeader = "Research ID\tQuery ID\tQuestion ID\tAnswer ID\tUsername\tTime\tQuestion\tAnswer\tType\n";

		if(!$file = fopen($filename,'a')){
			return false;
		}
		if(fwrite($file,$txtFileHeader)===FALSE){
			return false;
		}
		
		$sql = new AnswerSQLQueryer();
		$answers  = $sql->getResearchAnswers($research_id);
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
				$path = full_url();
				$replace = "functionality";
				$path = str_replace($replace,"content",$path);
				$path = pathinfo($path);
				$path =  $path['dirname'];
				$answer = $path."/showpic.php?picID=".$aid;
			}
			
			/*if ($i==0) {
				$prevUsr = $usr;
				$prevQrid = $qrid;
				$txtRow = "$rid\t$qrid\t$qid\t$aid\t$usr\t$time\t$nr $question\t".utf8_decode($answer)."\t$type";
			} elseif ($usr == $prevUsr && $qrid == $prevQrid) {
				$txtRow = $txtRow."\t$nr $question\t".utf8_decode($answer)."\t$type";
			} else {
				$txtFile = $txtRow."\n";
				$txtRow = "$rid\t$qrid\t$qid\t$aid\t$usr\t$time\t$nr $question\t".utf8_decode($answer)."\t$type";
				$prevUsr = $usr;
				$prevQrid = $qrid;
			}*/
			
			// $txtFile = "$rid\t$qrid\t$qid\t$aid\t$usr\t$time\t$nr $question\t".utf8_decode($answer)."\t$type\n"; //commment not by huiwen
			$txtRow = "$rid\t$qrid\t$qid\t$aid\t$usr\t$time\t$nr $question\t".utf8_decode($answer)."\t$type";
			$txtFile .=   $txtRow."\n";

			//$txtFile = $txtRow."\n";
			//$txtRow = "$rid\t$qrid\t$qid\t$aid\t$usr\t$time\t$nr $question\t".utf8_decode($answer)."\t$type";
		}
			if(fwrite($file,$txtFile)===FALSE){
					return false;
			}
		return true;
		
	}//end of writeAnswerTxt
	
	/*
	 * Scans the file media folders and add each files to a ZIP file.
	 */
	function zipMediaFiles($research_id){
		$zip = new ZipArchive;
		$dir = "../MediaFiles/Research_$research_id";
		if(is_dir($dir)){	// checks if directories exist
			$filename = "$dir/Research_$research_id.zip";
			//echo $filename."<br />";
			if($zip->open($filename,ZIPARCHIVE::CREATE)!== TRUE){	// opens a ZIP archive
				return "Error";
			}
			$sdir = scandir($dir);	// List files and directories
			$dirs = count($sdir);	// Count all elements in an array 
			for($i=0;$i<$dirs;$i++){	// in every directory
				$sdir[$i] = trim($sdir[$i],".");	//scans the subdirectories ...
				if(!empty($sdir[$i])){
					$dir2 = "$dir/$sdir[$i]";
					$sdir2 = scandir($dir2);
					$dirs2 = count($sdir2);
					for($j=0;$j<$dirs2;$j++){
						$sdir2[$j] = trim($sdir2[$j],".");
						if(!empty($sdir2[$j])){
							$dir3 = "$dir2/$sdir2[$j]";											                          
							$sdir3 = scandir($dir3);
							$dirs3 = count($sdir3);
							for($k=0;$k<$dirs3;$k++){
								$sdir3[$k] = trim($sdir3[$k],".");
								if(!empty($sdir3[$k])){								
									$path = "$dir3/$sdir3[$k]";
									$zipEntryName = $sdir3[$k];
	   								$this->addFileToZip($filename,$path,$zipEntryName);								
								}
							}
						}
					}
				}
			}
			return $filename;
		}else{
			return false;
		}
	}
	
	function addFileToZip($zipfile,$path,$zipEntryName){
		$zip = new ZipArchive;
		$res = $zip->open($zipfile, ZIPARCHIVE::CREATE);
		if($res === TRUE){
			$contents = file_get_contents($path);
			if($contents === false){
				return false;
			}
    		$zippi = $zip->addFromString($zipEntryName,$contents);
    		$zip->close();
   			return $zippi;
		}else{
    		return false;
		}
	}
	
	
}//end of class
?>
