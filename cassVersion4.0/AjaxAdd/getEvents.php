<?php
/*
 * This file is never called. 
 */
require_once("../common/includes.php");
function getQuestionType($quest) {
	$disStr = "";
	switch ($quest) {//Note: This conversion also exists on JavaScript code, at least in functions.js
		case "1": $disStr = "Open text"; break;
		case "2": $disStr = "Open number"; break;
		case "3": $disStr = "Sound"; break;
		case "4": $disStr = "Multiple choise"; break;
		case "5": $disStr = "Super"; break;
		case "6": $disStr = "Comment"; break;
		case "7": $disStr = "Photo"; break;
		case "8": $disStr = "Video"; break;
		case "9": $disStr = "Slider"; break;
	}
	return $disStr;
}


###MDB2 fetch start ###


//$qid = $_GET['id'];
$qid = 6;
echo("QID is $qid");

	if($qid!=null){
		$result = $mdb2->query("SELECT * FROM tbl_question WHERE query_id =$qid ORDER BY `number`"); 
		if (PEAR::isError($res)) {
		    die($res->getMessage());
	}
	echo("This is the result:");
	//var_dump($result);
	$apu=$result->getResource(); //This shit dun work
	// Insert a new row in the table for each person returned
	
	$i=0;
	while($row = mysql_fetch_array($apu)){
		$display_string .= '<div class="event" id="kyssa'.$row['question_id'].'" onClick="fade(\'kyssa'.$row['question_id'].'\')">';
		$display_string .= '<b>Question '. $row['number'] . '</b>';
		$display_string .= '<b>Question:</b> ' . $row['question'] . '<br />';
		$display_string .= '<p><b>Type:' . getQuestionType($row['question_type']) . '</b><br />';
		$display_string .= '<b>Category:</b> ' . $row['category'] . '<br />';
		$display_string .= '<b>Category:</b> ' . $row['category'] . '<br />';
		$display_string .= '</div>';
		$i++;
	}
	echo $display_string;
}else{
	echo("Error: No Query Specified");
}
?>