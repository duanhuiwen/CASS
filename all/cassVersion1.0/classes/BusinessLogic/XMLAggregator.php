<?php
/*
 * Used to create XML files for the phone and reads the XML which comes from the phone.
 */

class XmlAggregator{

function WriteXml($research_id,$query_id,$UID,$sCount){

	//Get the correct values for survey
	$user = new User($UID);
		$UName = $user->getName();
	$r = new Research($research_id);
	$colmet = $r->getCollMethod();
	if($colmet==2){
		$surveyTotal = "NaN";
	}else{
		$surveyTotal = $r->getQueriesPerDay();
	}	
	//create XML-document of the survey
	$survey = new DomDocument('1.0','ISO-8859-1');
	$survey->formatOutput =true;
	//Root element
	$root = $survey->createElement('survey');
	$root = $survey->appendChild($root);
	$root->setAttribute('username',$UName);
	$root->setAttribute('uid',$UID);
	$root->setAttribute('surveyId',$research_id);
	$root->setAttribute('surveyCount',$sCount);
	$root->setAttribute('surveyTotal',$surveyTotal);
	
	//Item element
	$q = new Question($query_id);
	$s = new Subject($UID);
	$sid = $s->getSubjectID();
	$privar = $s->getPrivar();
	$qs = $q->getQuestionArray($query_id);
	if($qs=="SelectError"){
		return false;
	}else{
	for($i=0;$i<count($qs);$i++){
		$qid = $qs[$i]['question_id'];
		$cat = $qs[$i]['category'];
		$type = $qs[$i]['question_type'];
		$quest = $qs[$i]['question'];
		$quest = str_replace("?", "%3F", $quest); // replacing "?" with "%3F" because of the client
		//Check if question needs private variables and replace [] with the correct variable
		if(isset($privar)){
			for($k=0;$k<=count($privar);$k++){
				for($v=0;$v<=count($privar);$v++){
					if($k == $privar[$v]['number']){
						$privarib = $privar[$v]['privar'];
						$replace = "[$k]";
						$quest = str_replace($replace,$privarib,$quest);
					}
				}
			}
		}
		if($type == 4 || $type == 5 || $type == 9 || $type == 2 || $type == 10){
			$que = new Question(0,$qid);
			//get option for super question
			$op = $que->getOptions();
		
			$item = $survey->createElement('item');
			$item->appendChild($survey->createTextNode(utf8_encode($quest)));
			$item->setAttribute('category',$cat);
			$item->setAttribute('type',$type);
			$item->setAttribute('q_id',$qid);
			if($type == 9){				
				$item->setAttribute('min',$op[1]['option']);
				$item->setAttribute('max',$op[3]['option']);
				$item->setAttribute('minlabel',utf8_encode($op[0]['option']));
				$item->setAttribute('maxlabel',utf8_encode($op[2]['option']));
				//$item->setAttribute('scale',$op[4]['option']);
			}elseif($type == 2){
				$item->setAttribute('min',$op[0]['option']);
				$item->setAttribute('max',$op[1]['option']);
			}
			$root->appendChild($item);
			//loop through options
			if($type == 4 || $type == 5 || $type == 10){
				for($j=0;$j<count($op);$j++){
					$val = $op[$j]['option'];
					$oid = $op[$j]['id'];
					$superOf = $op[$j]['super_of'];
					$option = $survey->createElement('option');
					$item->appendChild($option);
					if($type == 4 || $type == 10){					
						$option->setAttribute('value',utf8_encode($val));
						$option->setAttribute('o_id',$oid);
					}elseif($type == 5){
						$option->setAttribute('category',$superOf);
						$option->setAttribute('value',utf8_encode($val));
						$option->setAttribute('o_id',$oid);
					}
				//$option->setAttribute('category',$ocat);
				}//end of for	
			}		
		}else{
			$item = $survey->createElement('item');
			$item->appendChild($survey->createTextNode(utf8_encode($quest)));
			$item->setAttribute('category',$cat);
			$item->setAttribute('type',$type);
			$item->setAttribute('q_id',$qid);
			$root->appendChild($item);
		}
	}//end of for		
	}//end of if else
	
	//Save survey
	return $survey->saveXML();
	}//end of function writexml
	
	
function ReadXml($xml){
	//Open file
	$survey = new DOMDocument('1.0','ISO-8859-1');
	$survey->loadXML($xml);
	//read file
	$uidTag = $survey->getElementsByTagName('userName')->item(0);
	$uid = $uidTag->getAttributeNode('name')->value;
	$surveyIdTag = $survey->getElementsByTagName('surveyId')->item(0);
	$surveyId = $surveyIdTag->getAttributeNode('id')->value;
	//$timestampTag = $survey->getElementsByTagName('timestamp')->item(0);
	//$timestamp = $timestampTag->getAttributeNode('stamp')->value;
	/*
	                                                              * Might be problematic if the
	                                                              * phone gives the time when the
	                                                              * query was answered: time zone
	                                                              * difference, not punctual clock.
	                                                              */
	//all items to database
	$timestamp = date('Y-m-d H:i:s');
	$a = new Answer();
	foreach($survey->getElementsByTagName('item') as $itemTag){	
		$answer = $itemTag->getAttributeNode('answer')->value;
		$type = $itemTag->getAttributeNode('type')->value;
		$question_id = $itemTag->getAttributeNode('q_id')->value;
		$a->addAnswer($surveyId,$uid,$question_id,$timestamp,$answer);
	}
	$r = new Research($surveyId);
	if($r->getCollMethod()==1){
		$sub = new Subject($uid);
		$sub->setFixedAnswer();
	}

}//end of function readxml

}//end of class
?>
