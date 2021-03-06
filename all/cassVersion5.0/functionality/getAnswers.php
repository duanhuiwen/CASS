<?php
/*
 * This file is basically to retrieve data from the research. It makes calls to the following files:
 * - cass/classes/File-IO/FileIOHandler.php
 * - cass/classes/File-IO/FileIO.php
 * It also handles other data transform. It can create a respondent list with the
 * passwords and show the media files in the browser. 
 */
//Login form generation function include
require_once("../common/auth_loginf.php");
require_once("../common/auth_start.php");
if($_GET['action'] == "logout" && $a->checkAuth()){
    $a->logout();
    $a->start();
}else{
	if($a->checkAuth()){ ///Start secured content
		require_once("../common/includes.php"); //Class includes
		$UID=$a->getAuthData('uid'); //get user ID
		$u = new User($UID);
		// checking if the user has enough right to get the content
		if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
			echo "Access denied! Subjects can't login in to the admin tool.";
		}else{
			$id = decrypt($_GET['id']);		// gets the query id
			$rid = decrypt($_GET['rid']);	// gets the research id
			if(isset($_POST['subs']) || isset($_POST['mediadownload'])){
				$rid = decrypt($_POST['rid']);
			}
			/*
			 * If the query id was set then it gets the details of it.
			 * If not the query id was set, then it will create a research object.
			 */
			if(!empty($id)){			
				$query = new Query($id);
				$owner = $query->getOwner();
				$r = new Research($owner);
			}elseif(!empty($rid)){
				$r = new Research($rid);
			}
			// checks if the user has sufficient right
			if($r->users->isLocalAdmin($UID) || $r->users->isLocalResearcher($UID)){
				/*
				 * If the GET parameter is set to getTxt or getXls
				 * then it initializes the $ext variable which will be the extension of
				 * the file that can be downloaded.
				 * All the processes here uses the FileIOHandler class:
				 * cass/classes/FileIOHandler.php
				 */
				/*
				 * This part of the script creates an excel sheet or a text file from
				 * the collected answers.
				 * It is called in ./functionality/displayQuery.php
				 */
				if(isset($_GET['getTxt']) || isset($_GET['getXls'])){
					if(isset($_GET['getXls'])){
						$ext = "xls";
					}else{
						$ext = "txt";
					}
					//ini_set('memory_limit', '12M');
					$f = new FileIOHandler();
					$filename = "../temp/Query_$id"."_Answers.$ext";
					if($f->getQueryAnswers($id,$ext)){
						$filesize= filesize($filename);
						header ("Content-Type: application/download");
						header ("Content-Disposition: attachment; filename=$filename");
						header("Content-Length: $filesize");
						$fp = fopen("$filename", "r");
						$thru = fpassthru($fp);
						fclose($fp);
						$dir = "../temp/";
						$fname = "Query_".$id."_Answers.$ext";
						if($thru == $filesize){
							chdir($dir);
		    				unlink($fname);
						}	
					}
				/*
				 * This part returns the questions in the actual query.
				 * It is called in ./functionality/displayQuery.php
				 */
				}elseif(isset($_GET['getQuery'])){
					$f = new FileIOHandler();
					$data = $f->writeQuery($id);
					$filename = "../temp/query_".$id."_questions.rtf";
					$filename = $f->writeFile($filename,$data);
					$filesize= filesize($filename);
					header("Content-Type: application/download");
					header("Content-Disposition: attachment; filename=$filename");
					header("Content-Length: $filesize");
					$fp = fopen("$filename", "r");
					$thru = fpassthru($fp);
					fclose($fp);
					$dir = "../temp/";
					$fname = "query_".$id."_questions.rtf";
					if($thru == $filesize){
						chdir($dir);
						//chmod($dir, 0777);
    					unlink($fname);
					}
				/*
				 * This part of the code will open a new file object to save the
				 * questions in the research in rtf format.
				 * Called in ./functionality/displayResearch.php
				 */
				}elseif(isset($_GET['getResearch'])){
					$f = new FileIOHandler();
					// collect the data to be written in the file
					$data = $f->writeResearch($rid);
					$filename = "../temp/research_".$rid.".rtf";
					// writeFile function opens, writes and closes the file
					$filename = $f->writeFile($filename,$data);
					$filesize= filesize($filename);
					header("Content-Type: application/download");	// sends a raw http header to start download
					header("Content-Disposition: attachment; filename=$filename");	// it names the file
					header("Content-Length: $filesize"); /*
					                                      * The Content-length header is useful to set for downloads.
					                                      * The browser will be able to show a progress meter as a file downloads.
					                                      * The content-length can be determined by filesize function that returns
					                                      * the size of a file.
					                                      */
					$fp = fopen("$filename", "r");
					/*
					 * Reads to EOF on the given file pointer from the current position
					 * and writes the results to the output buffer.
					 * The $thru variable stores the number of characters read from handle
					 * and passed through to the output.
					 */ 
					$thru = fpassthru($fp);
					fclose($fp);
					$dir = "../temp/";
					$fname = "research_".$rid.".rtf";
					if($thru == $filesize){
						chdir($dir);
						//chmod($dir, 0777);
    					unlink($fname);
					}
				/*
				 * This part returns all the media files that were given as answers
				 * packaged into a zip file.
				 * Called in ./functionality/displayResearch.php
				 */	
				}elseif(isset($_GET['getResearchZip'])){
					$f = new FileIOHandler();
					$data = $f->zipMediaFiles($rid);
					if($data!=false){	// I'm afraid this variable cannot takes this state.
						$fname = "Research_".$rid.".zip";
						$filename = "../MediaFiles/Research_".$rid."/Research_".$rid.".zip";
						if($f->getFiles2Db()==false){
							$filesize= filesize($filename);
							header("Content-Length: $filesize");
						}
						header("Pragma: public");
						header("Expires: 0");	// set expiration time
						header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
						// force download dialog
						header ("Content-Type: application/force-download");
						header ("Content-Type: application/octet-stream");
						header ("Content-Type: application/download");
						header ("Content-Disposition: attachment; filename=$fname");
						if($f->getFiles2Db()){
							header("Content-Transfer-Encoding: binary");
							$fp = fopen($filename, "rb");
						}else{
							$fp = fopen($filename, "r");
						}
						$thru = fpassthru($fp);
						fclose($fp);
						$dir = "../MediaFiles/Research_".$rid."/";
						if($thru == $filesize){
							chdir($dir);
							//chmod($dir, 0777);
		    				unlink($fname);
						}
					}else{
						echo "There are no mediafiles in this research! data:<br />$data";
					}
				/*
				 * Returns the user info:
				 * username, password and token
				 * Called in ./functionality/displayResearch.php 
				 */
				}elseif(isset($_GET['getUserInfo'])){
					$f = new FileIOHandler();
					$filename = "../temp/Research_".$rid."_subjects.rtf";
					$data = $f->writeUserInfo($rid);
					//writeFile function opens, write and closes the file
					$filename = $f->writeFile($filename, $data);
					$filesize = filesize($filename);
					header("Content-Type: application/download"); //sends a raw http header to start download
					header("Content-Disposition: attachment; filename=$filename");
					header("Content-Length: $filesize");/*
					                                      * The Content-length header is useful to set for downloads.
					                                      * The browser will be able to show a progress meter as a file downloads.
					                                      * The content-length can be determined by filesize function that returns
					                                      * the size of a file.
					                                      */

					$fp = fopen("$filename", "r");
					/*
					 * Reads to EOF on the given file pointer from the current position
					 * and writes the results to the output buffer.
					 * The $thru variable stores the number of characters read from handle
					 * and passed through to the output.
					 */ 
					$thru = fpassthru($fp);
					fclose($fp);
					$dir = "../temp/";
					$fname = "research_".$rid."_subjects.rtf";
					if($thru == $filesize){
						chdir($dir);
						//chmod($dir, 0777);
    					if (is_file($fname)){
    						unlink($fname);	
    					}					
				}
				/*
				 * Returns all the answers in the research in a certain format.
				 * Called in ./functionality/displayResearch.php
				 */
				}elseif(isset($_GET['getResearchAnswers'])){
					//ini_set('memory_limit', '12M');
					$f = new FileIOHandler();
					$filename = "../temp/Research_".$rid."_Answers.xls";
					/*
					 * If the data writing is succesful then it returns true.
					 * Sends the headers for downloading. Opens the file for sending and sends
					 * it over. Finally, it deletes the file that was created.  
					 */
					if($f->getResearchAnswers($rid)){
						$filesize= filesize($filename);
						header ("Content-Type: application/download");
						header ("Content-Disposition: attachment; filename=$filename");
						header("Content-Length: $filesize");
						$fp = fopen("$filename", "r");
						$thru = fpassthru($fp);
						fclose($fp);
						$dir = "../temp/";
						$fname = "Research_".$rid."_Answers.xls";
						if($thru == $filesize){
							chdir($dir);
		    				unlink($fname);
						}	
					}
				/*
				 * Returns all the answers in the research in a certain format.
				 * Called in ./functionality/displayResearch.php
				 */
		 		}elseif(isset($_GET['getResearchAnswers2'])){
					//ini_set('memory_limit', '12M');
					$f = new FileIOHandler();
					$filename = "../temp/Research_".$rid."_Answers_v2.xls";
					/*
					 * If the data writing is succesful then it returns true.
					 * Sends the headers for downloading. Opens the file for sending and sends
					 * it over. Finally, it deletes the file that was created.  
					 */	
					if($f->getResearchAnswers2($rid)){
						$filesize= filesize($filename);
						header ("Content-Type: application/download");
						header ("Content-Disposition: attachment; filename=$filename");
						header("Content-Length: $filesize");
						$fp = fopen("$filename", "r");
						$thru = fpassthru($fp);
						fclose($fp);
						$dir = "../temp/";
						$fname = "Research_".$rid."_Answers_v2.xls";
						if($thru == $filesize){
							chdir($dir);
		    				unlink($fname);
						}	
					}
				/*
				 * Returns all the answers in the research in a certain format.
				 * Called in ./functionality/displayResearch.php
				 */
		 		}elseif(isset($_GET['getResearchAnswers3'])){
					//ini_set('memory_limit', '12M');
					$f = new FileIOHandler();
					$filename = "../temp/Research_".$rid."_Answers_v3.xls";
					/*
					 * If the data writing is succesful then it returns true.
					 * Sends the headers for downloading. Opens the file for sending and sends
					 * it over. Finally, it deletes the file that was created.  
					 */
					if($f->getResearchAnswers3($rid)){
						$filesize= filesize($filename);
						header ("Content-Type: application/download");
						header ("Content-Disposition: attachment; filename=$filename");
						header("Content-Length: $filesize");
						$fp = fopen("$filename", "r");
						$thru = fpassthru($fp);
						fclose($fp);
						$dir = "../temp/";
						$fname = "Research_".$rid."_Answers_v3.xls";
						if($thru == $filesize){
							chdir($dir);
		    				unlink($fname);
						}	
					}
				/*
				 * Returns a file with the data of the newly created respondents.
				 * Called in ./functionality/addnsubjects.php
				 */
		 		}elseif(isset($_POST['subs'])){		 			
		 			$f = new FileIOHandler();
					$data = $_POST['subs'];
					$filename = "../temp/Research_".$rid."_subjects.rtf";
					$filename = $f->writeFile($filename,$data);
					$filesize= filesize($filename);
					header ("Content-Type: application/download");
					header ("Content-Disposition: attachment; filename=$filename");
					header("Content-Length: $filesize");
					$fp = fopen("$filename", "r");
					$thru = fpassthru($fp);
					fclose($fp);
					$dir = "../temp/";
					$fname = "Research_".$rid."_subjects.rtf";
					if($thru == $filesize){
						chdir($dir);
    					unlink($fname);
					}
				/*
				 * Returns the picture from the database, or from the server in a
				 * format that the browser could show.
				 * Called in ./content/showpic.php
				 */
		 		}elseif(isset($_POST['mediadownload'])){
		 			$aid = decrypt($_POST['aid']);
		 			$an = new Answer($aid);
		 			$fname = $an->getMediaFileName();
		 			$f = new FileIOHandler();
		 			if($f->getFiles2Db()){
		 				$filename = "../temp/".$fname;
		 				$type = $an->getType();					
						$data = $f->MediaRead($type,$aid);
						$filename = $f->writeFile($filename,$data);					
		 			}else{
		 				$filename = $an->getMediaFilePath();
		 			}
		 			$filesize= filesize($filename);
					header ("Content-Type: application/download");
					header ("Content-Disposition: attachment; filename=$filename");
					header("Content-Length: $filesize");
					$fp = fopen("$filename", "r");
					$thru = fpassthru($fp);
					fclose($fp);
					$dir = "../temp/";
					if($thru == $filesize && $f->getFiles2Db()){
						chdir($dir);
    					unlink($fname);
					}	
		 		}
			}
		}
	}
}
?>
