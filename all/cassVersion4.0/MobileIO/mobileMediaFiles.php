<?php
 //includes
 require_once("../common/includes.php");
 // processing data stream
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
 $q = new Question(0,$q_id);
 $answers = $q->listChildren();
 $type = $q->getQuestionType();
	
 if(count($answers)>0){
 	$fileIO = new FileIOHandler();
 	for($i=0;$i<count($answers);$i++){
 		$a = new Answer($answers[$i]['answer_id']);
 		if($fname == $a->getMediaFileName()){
			$fileIO->MediaWrite($data, $type,$answers[$i]['answer_id'],$fname);
 		}	
 	}
 }else{
 	/*
 	 * 	T�h�n logimerkint� jos ei onnistunu!
	 */
 	//write file
	$file = fopen("../temp/debugError.txt","w");
	$data = "TYPE: $type NAME: $fname";
	fwrite($file,$data);
	fclose($file);
 }

?>