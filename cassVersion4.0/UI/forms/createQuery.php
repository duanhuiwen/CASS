<script type="text/javascript">
<!--
	function clearInput(id){
		document.getElementById(id).value = "";
	}
//-->
</script>

<?php
if($r->users->isLocalAdmin($UID)){
	if($r->isLocked()!="Ended" && $r->isLocked()!="freezed"){
		$queries = mysql_numrows($r->listChildren());
		$qPerDay = $r->getQueriesPerDay();
		$colmet = $r->getCollMethod();  

?>
<div id="addresearchdiv">
<?php
if($queries>=$qPerDay){
	if($colmet==0 || $colmet==1){
		echo "NOTE! There are more queries in the research than there are sent!<br />";
		echo "This query may never be sent";
	}else{
		$colmet2str = $r->collMethod2String($colmet);
		echo "NOTE! With $colmet2str data collection only ONE query is sent!<br />";
		//echo "This query may never be sent";
	}
}
?>
<form id="addqueryform" action="./addQuery_act.php" method="post" enctype="multipart/form-data">
  <div class="descrheader"><div class="headertext"><div id="help" onclick="openhelp('addquer');">?</div><h1>Add new query</h1><hr /></div></div>
  <div class="addresearchcontent"> 	
  <div id="invalidform"><div class="errortxt"></div></div>
  	<h2>Query name</h2>
  	<input type="text" id="name" name="name" value="The name or designation of the query" size="40" maxlength="40" onClick="clearInput('name')" onFocus="clearInput('name')"/><div class="qnameerror"></div>
  	<input type="hidden" name="researchid" value="<?php echo $researchID; ?>" /><br />
  <br />
  <?php  
  if($colmet==0){
  	$queryTimes = $r->getQueryTimes();
  	if(mysql_numrows($queryTimes)>0){  	 
  		echo "<h2>Assign query to specific time</h2>"; 	
  		for($i=0;$i<mysql_numrows($queryTimes);$i++){
	  		$qtime_id = mysql_result($queryTimes,$i,"qtime_id");
	  		$qtime = mysql_result($queryTimes,$i,"qtime");
	  		$query_id = mysql_result($queryTimes,$i,"query_id");
	  		echo "<b>$qtime</b><input type=\"checkbox\" name=\"qtime[$i]\" id=\"qtime[$i]\" value=\"$qtime_id\" class=\"checkInput\" ";
  			if($query_id!=0){
	  			echo ' disabled="true"';
	  		}
	  		echo '/>';
	  		if($query_id!=0){
	  			$q = new Query($query_id);
	  			$name = $q->getName();
	  			echo "Time assigned for query: $name";
	  		}
	  		echo "<br />";
	  	}
  		echo "<input type=\"hidden\" name=\"querytimes\" value=\"".mysql_numrows($queryTimes)."\" />";
  	}
  }elseif($colmet==1){
  	$queryFixedTimes = $r->getFixedTimes();
  	$firsttime = $r->getFixedFirsttime();
  	$interval = $r->getFixedInterval();
  	if(mysql_numrows($queryFixedTimes)>0){  	 
  		echo "<h2>Assign query to an interval</h2>"; 	
  		for($i=0;$i<mysql_numrows($queryFixedTimes);$i++){
	  		$fixedtime_id = mysql_result($queryFixedTimes,$i,"fixedtime_id");
	  		$fixedtime = mysql_result($queryFixedTimes,$i,"fixedtime");
	  		$query_id = mysql_result($queryFixedTimes,$i,"query_id");
	  		if($fixedtime==1){
	  			echo "<b>First query sent at $firsttime o'clock</b> <input type=\"checkbox\" name=\"fixedtime[$i]\" id=\"fixedtime[$i]\" value=\"$fixedtime_id\" class=\"checkInput\" ";
	  			if($query_id!=0){
	  				echo ' disabled="true"';
	  			}
	  		}else{
	  			echo "<b>After $fixedtime. interval ($fixedtime x $interval)</b><input type=\"checkbox\" name=\"fixedtime[$i]\" id=\"fixedtime[$i]\" value=\"$fixedtime_id\" class=\"checkInput\" ";
	  			if($query_id!=0){
	  				echo ' disabled="true"';
	  			}
	  		}
	  		echo '/>';
	  		if($query_id!=0){
	  			$q = new Query($query_id);
	  			$name = $q->getName();
	  			echo "Time assigned for query: $name";
	  		}
	  		echo "<br />";
	  	}
  		echo "<input type=\"hidden\" name=\"fixedtimes\" value=\"".mysql_numrows($queryFixedTimes)."\" />";
  	}
  }
  ?>  
  <p><br />
  	<!-- <input type="submit" name="submit" value="Add Query"/> -->
  	<a class="button" href="#" onclick="this.blur();validateAddQuery('addqueryform')"><span>Create query</span></a>
  </p>
  </div>
  </form>  
  </div>
 <?php }else{
 			echo "<h1>Research has ended! Cannot edit it anymore</h1>";
 	   }
 } ?>