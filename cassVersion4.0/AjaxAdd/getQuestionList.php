<?php
/*
 * This script gets the list of the questions in the query and makes a list from them. The list
 * is created as a sortable list using the jQuery UI JavaScript library.
 * When the user clicks on the question it will appear on the Edit side. This is made by the fade
 * function which can be found in functions.js. This function draws a border around the question.
 * The showEdit function (in functions.js) calls the clientSideInclude function with a question
 * id (qid). The clientSideInclude function takes two parameters: an id and a url.
 * It pushes the content of the url into the given id tag.
 * The file is AjaxAdd/modifyQuestion_frm.php?id='+qid. This will appear under the edit tag.
 */
require_once("../common/includes.php");

$qid = $_GET['id'];

if(isset($qid)){
	$query = new Query($qid);
	$ifVisualize = $query->isVisualize();
	$qlist = $query->listChildren();
	$numOfQ = $query->getNumOfQuestions();
	$display_string = "Questions:";
	$display_string .="<ul id=\"questionList\">";

	// Insert a new row in the table for each person returned
	if($qlist!=null && $numOfQ>0){
		while($row = mysql_fetch_array($qlist)){
			$display_string .="<li id=\"item_".$row['question_id']."\">";
			$display_string .= '<div class="event" id="kyssa'.$row['question_id'].'" onClick="fade(\'kyssa'.$row['question_id'].';'.$qid.'\',\''.$row['number'].';'.$numOfQ.'\'),selecta(0)">';
			$display_string .= '<div class="inEvent"><h3 class="questionNum">Question '. $row['number'] . '</h3>';
			$display_string .= '<p><b>Question: </b> ' . $row['question'] . '<br />';
			$display_string .= '<b>Type: </b>' . getQuestionType($row['question_type']) . '<br />';
			$display_string .= '<b>Category: </b> ' . $row['category'] . '<br /></p>';
			if($row['category']!=0){
				$q = new Question(0,$row['question_id']);
				$parentti = $q->getParentQuestion();
				if(!empty($parentti)){
					$p = new Question(0,$parentti);
					$parentti = $p->getQuestionText();
					$display_string .= 'Parent question: '.$parentti.'';
				}
			}
			$display_string .= '</div></div>';
			$display_string .="</li>";
		}
	}else{
		$display_string .="<br /><br /><br />No question<br /><br /><br />";
	}
	$display_string .="</ul>";
	echo $display_string;
	

}else{
	echo("Error: No Query Specified");
}
?>