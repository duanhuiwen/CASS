<?php

if($r->users->isLocalAdmin($UID) || $r->users->isLocalResearcher($UID)){
	if($id!=null){
		$enId = encrypt($id);
		$name= $query->getName();	// $query has been initialized in content/displayQuery.php
		$queNum = $query->getNumOfQuestions();
		$owner = $query->getOwner();
		$reName = $r->getName();
		$rStart = $r->getStartTime();
		$rEnd = $r->getEndTime();
		$rStatus = $r->getStatus();
		$rLocked = $r->isLocked();
		$colmet = $r->getCollMethod();
		$queryTimes = $query->getQueryTime();
		$fixedTimes = $r->getFixedTimes($id);
		$questions = $query->listChildren();
		for($i=0;$i<mysql_numrows($questions);$i++){
			$question_id = mysql_result($questions,$i,"question_id");
			$question = new Question(0,$question_id);
			$ansNum = $ansNum + count($question->listChildren());
		}
			
		// Content in center
		// Navigation bar
		echo "<div id=\"navpath\"><a href=\"../content/index.php\"> Home </a> >> <a href=\"../content/displayResearch.php?id=$research_id\"> ". $reName ." </a> >> <a href=\"#\"> ". $name ." </a></div>";
		echo "<div id=\"diplayquerydiv\">";
		// Name of the query
		echo("<div class=\"descrheader\"><div class=\"headertext\"><h1>$name</h1><hr /></div></div>");
		// From which research and the duration of the research
		echo("<div class=\"description\"><b>Query from research:</b> $reName <div class=\"descrtext\">During: $rStart - $rEnd</div></div>");
		// If it is a fixed time research and any time is appended to the query, it will print it
		if(mysql_numrows($queryTimes)>0 && $colmet==0){
			echo "<div class=\"description\"><b>Query is sent ".mysql_numrows($queryTimes)." times a day at:</b>";
			echo "<div class=\"descrtext\">";
			for($j=0;$j<mysql_numrows($queryTimes);$j++){
				$qtime = mysql_result($queryTimes,$j,"qtime");
				echo "$qtime<br />";		
			}
			echo "</div></div>";
		/* If the research is fixed interval research, it will print out the interval and after
		 * how many intervals the query is sent. */
		}elseif($colmet==1 && mysql_numrows($fixedTimes)>0){
			echo "<div class=\"description\"><b>Query is sent ".mysql_numrows($fixedTimes)." times a day at:</b>";
			echo "<div class=\"descrtext\">";
			$firsttime = $r->getFixedFirsttime();
			$interval = $r->getFixedInterval();
			for($j=0;$j<mysql_numrows($fixedTimes);$j++){
				$fixedtime = mysql_result($fixedTimes,$j,"fixedtime");
				if($fixedtime==1){
					echo "Sent $firsttime o'clock";
				}else{
					echo "Sent after $fixedtime. interval ($fixedtime x $interval)<br />";
				}		
			}
			echo "</div></div>";
		}else{
			if($colmet==0 || $colmet==1){
				echo "<div class=\"description\"><b>Query is never sent</b></div>";
			}
		}
		if(empty($queNum)){
			$queNum="no";
		}
		echo"<div class=\"description\"><h2>Questions</h2><div class=\"descrtext\">There are $queNum questions in this query<br />";
		echo "<a href=\"#\" onclick=\"getfiles('getQuery','$enId')\"> >> Print questions</a></div></div>";
		if($rStatus!="Starts"){
			echo"<div class=\"description\"><h2>Answers</h2><div class=\"descrtext\">There are $ansNum answers to this query<br />";
			// AjaxAdd/functions.js -> functionality/getAnswers.php
			echo "<a href=\"#\" onclick=\"getfiles('getTxt','$enId')\"> >> Print answers in text file</a><br />";
			echo "<a href=\"#\" onclick=\"getfiles('getXls','$enId')\"> >> Print answers in excel file</a></div></div>";
		}
		echo "<br />";
		echo '<p><a class="button" href="../content/displayResearch.php?id='.$research_id.'"><span>Display research</span></a></p>'; 
		echo "</div>";
		echo "</div>";
		//Div element in the right
		echo "<div id=\"right\">";
		echo "<div id=\"rightContent\">";
		if($rLocked==false && $r->users->isLocalAdmin($UID)){
			$enId = encrypt($id);
			echo "<div class=\"actiontitle\"><div class=\"actiontext\">Actions</div></div>";
			// Removing the query
			echo "<div class=\"menuitem\"><a href=\"#\" onClick=\"wantToRemoveQuery('$enId')\"> >> Remove this query</a></div>";
			// Edit the questions of the query
			echo "<div class=\"menuitem\"><a href=\"./manipQuery.php?id=$enId\"> >> Edit questions</a></div>";
			// Edit the information of the query
			echo "<div class=\"menuitem\"><a href=\"./editQuery.php?id=$enId\"> >> Edit query information</a></div>";
		}
		// If the research is on progress then it goves the possibility to freeze it.
		if($rStatus=="On Progress"){
			if($rLocked=="freezed"){
				echo "<div id=\"freezed\"><u>RESEARCH IS LOCKED!</u></div>";
			}
		}
		echo "</div>";
	}else{
		echo("<h1>Error: No query specified!</h1>");
	}
}
?>