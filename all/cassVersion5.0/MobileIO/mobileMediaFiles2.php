<?php
 //includes
 require_once("../common/includes.php");
 // processing data stream
 
 
 //source = html5 means the uploaded file is from html5 client
 $source = null;
 

//the name attribute of the file input should be image 
 
 if($_POST['qid'] != null && $_FILES['image']['tmp_name'] != null &&  $_FILES['image']['name'] != null){
 	
 	$temp_file_path = $_FILES['image']['tmp_name'];
 	
 	$q_id = $_POST['qid'];
 	$fname = $_FILES['image']['name'];
 	$source = "html5";
 	echo "inside first if</br>";
 }
 
 else{
 
 
 $filename = substr($HTTP_RAW_POST_DATA, 0, 128);
 $filename = trim($filename, " ");
 $filename = explode(";",$filename);
 $fname = $filename[0];
 $q_id = $filename[1];
 $data = substr($HTTP_RAW_POST_DATA, 128);
 /*$file = fopen("../temp/debug1.txt","w");
			$data = "QID: $q_id NAME: $fname";
			fwrite($file,$data);
			fclose($file);*/
 }
 $q = new Question(0,$q_id);
 $answers = $q->listChildren();
 $type = $q->getQuestionType();
	
 if(count($answers)>0){
 	$fileIO = new FileIOHandler();
 	for($i=0;$i<count($answers);$i++){
 		$a = new Answer($answers[$i]['answer_id']);
 		if($fname == $a->getMediaFileName()){
 			if($source == "html5"){
 				$fileIO->MediaWrite1($temp_file_path,$type,$answers[$i]['answer_id'],$fname);
 				
 				echo "<br>inside meidawrite1";echo "$temp_file_path<br>";
 			}else{
 				$fileIO->MediaWrite($data, $type,$answers[$i]['answer_id'],$fname);
 			}
			
 		}	
 	}
 }else{
 	/*
 	 * 	Tï¿½hï¿½n logimerkintï¿?jos ei onnistunu!
	 */
 	//write file
	$file = fopen("../temp/debugError.txt","w");
	$data = "TYPE: $type NAME: $fname";
	fwrite($file,$data);
	fclose($file);
 }

?>