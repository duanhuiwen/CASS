<?php
/*
 * This file prints out the list of the questions in a given query. From this list
 * the user can drag questions into the #eventList unordered list. It will copy all
 * the values and creates a new question with a new question id.
 * It is called from the showCopy.php.
 */
require_once("../common/includes.php");

$qid = $_GET['id'];
$copyto = $_GET['copyto'];

if(isset($qid)){
	$query = new Query($qid);	// creates a new Query object
	$qlist = $query->listChildren();	// creates a list of questions
	$qname = $query->getName();	// gets the name of the query
	$numOfQ = $query->getNumOfQuestions();	// get the number of the questions
	/*
	 * The following part sets a variable to store the actual HTML code to be printed.
	 * It draws the buttons to copy the whole query, a button to get back to show the
	 * list of the queries, the name of the query and starts an unordered list for the
	 * question in the actual query.
	 */
	$display_string .="<div class=\"copyAll\"><a class=\"button\" href=\"#\" onclick=\"this.blur();copyAll($copyto,$qid)\"><span>Copy whole query</span></a></div>";
	$display_string .="<div class=\"backbutton\"><a class=\"button\" href=\"#\" onclick=\"this.blur();rewind(1)\"><span>Back</span></a></div>";
	$display_string .="<br /><h3>$qname</h3>"; 
	$display_string .="<ul id=\"copyQuestionList\" style=\"margin:0px\">";
	
	if($qlist!=null){ // if there are questions in the query creates a list from them.
		$j = 0;
		while($row = mysql_fetch_array($qlist)){
			$j++;
			$display_string .="<li id=\"copy_".$row['question_id']."\">";
			$display_string .= '<div class="event" id="c_event'.$row['question_id'].'">';
			$display_string .= '<div class="inEvent">';
			$display_string .='<h3 class="questionNum">Question '.$j.'</h3>';
			$display_string .= '<b>Question: </b> ' . $row['question'] . '<br />';
			$display_string .= '<b>Type: </b>' . getQuestionType($row['question_type']) . '<br />';
			$display_string .= '<b>Category: </b> ' . $row['category'] . '<br />';
			if($row['question_type']==2 || $row['question_type']==4 || $row['question_type']==5 || $row['question_type']==10){
				$q = new Question(0,$row['question_id']);
				$ops = $q->getOptions();
				if(!empty($ops)){
					if($row['question_type']==4 || $row['question_type']==5 || $row['question_type']==10 ){
						$display_string .= "<b>Options:</b> <br /><p>";
						for($i=0;$i<count($ops);$i++){
							$option = $ops[$i]['option'];
							$display_string .= $i+1 .". $option<br />";
						}
						$display_string .="</p>";
					}elseif($row['question_type']==2){
						$display_string .= "<b>Values:</b> <br />";
						$display_string .= "<p>Min: ". $ops[0]['option'] ."<br />";
						$display_string .= "Max: ". $ops[1]['option'] ."</p>";
					}elseif($row['question_type']==9){
						$display_string .= "<b>Values:</b> <br />";
						$display_string .= "<p>Min. label: ". $ops[0]['option'] ."<br />";
						$display_string .= "Min. value: ". $ops[1]['option'] ."<br />";
						$display_string .= "Max. label: ". $ops[2]['option'] ."<br />";
						$display_string .= "Max. value: ". $ops[3]['option'] ."<br />";
						$display_string .= "Scale: ". $ops[4]['option'] ."</p>";
					}
				}
			}
			$display_string .= '</div></div>';
			$display_string .="</li>";
		}
		$display_string .="</ul>";
		echo $display_string;
	}
}else{
	echo("Error: No Query Specified");
}
?>