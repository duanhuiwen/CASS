<?php

if($res->users->isLocalAdmin($UID)){
		$locked = $res->isLocked();
		if(($query->isLocked()==false && $locked!="Ended" && $locked!="freezed") || $query->isLocked()==$UID){ 
			$query->setLocked($UID);
			echo "<div id=\"navpath\"><a href=\"../content/index.php\"> Home </a> >> <a href=\"../content/displayResearch.php?id=$research_id\"> ". $res->getName() ." </a> >> <a href=\"../content/displayQuery.php?id=".encrypt($id)."\"> ". $query->getName() ." </a> >> <a href=\"#\"> Edit query </a></div>";
?>
<div id="diplayquerydiv">
<div class="descrheader">
	<div class="headertext">
		<h1>Update Query</h1><hr />
	</div>
</div>
<a class="backlink" href="../content/displayQuery.php?id=<?=encrypt($id);?>"> <- Back</a>
<div class="description">
<form id="updatequeryform" action="./editQuery_act.php" method="post" enctype="multipart/form-data">
	<div id="invalidform"><div class="errortxt"></div></div>
  	<h2>Query name</h2><br />
  	<input type="text" id="name" name="name" value="<?php echo $qname; ?>" size="40" maxlength="40"/><div class="qnameerror"></div><br />
  	<input type="hidden" name="qid" value="<?php echo encrypt($id); ?>" /><br />
  <br />
  <?php
  /*
   * Prints the different times or orders of the queries as they are assigned in the research.
   * Makes it possible to change which time or order the query is assigned.
   */
  if($colmet==0){
  	$queryTimes = $res->getQueryTimes();
  	if(mysql_numrows($queryTimes)>0){  	 
  		echo "<h2>Assign query to specific time</h2>"; 	
  		for($i=0;$i<mysql_numrows($queryTimes);$i++){
	  		$qtime_id = mysql_result($queryTimes,$i,"qtime_id");
	  		$qtime = mysql_result($queryTimes,$i,"qtime");
	  		$query_id = mysql_result($queryTimes,$i,"query_id");
	  		echo "<b>$qtime</b> <input type=\"checkbox\" name=\"qtime[$i]\" id=\"qtime[$i]\" value=\"$qtime_id\"";
	  		if($query_id==$id){
	  			echo " checked=\"checked\" ";	
	  		}elseif($query_id!=0){
	  			echo ' disabled="true"';
	  		}
	  		echo " />";
	  		if($query_id==$id){
	  			echo "<input type=\"hidden\" name=\"unchecked[$i]\" value=\"$qtime_id\" />";
	  		}
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
  	$fixedTimes = $res->getFixedTimes();
  	$firsttime = $res->getFixedFirsttime();
  	$interval = $res->getFixedInterval();
  	if(mysql_numrows($fixedTimes)>0){
  		echo "<h2>Assign query to specific order</h2>";
  		for($i=0;$i<mysql_numrows($fixedTimes);$i++){
  			$fixedtime_id = mysql_result($fixedTimes,$i,"fixedtime_id");
	  		$fixedtime = mysql_result($fixedTimes,$i,"fixedtime");
	  		$query_id = mysql_result($fixedTimes,$i,"query_id");
  			if($fixedtime==1){
	  			echo "<b>First query sent at $firsttime o'clock</b> <input type=\"checkbox\" name=\"fixedtime[$i]\" id=\"fixedtime[$i]\" value=\"$fixedtime_id\" class=\"checkInput\" ";
	  			if($query_id==$id){
	  				echo " checked=\"checked\" ";
	  			}elseif($query_id!=0){
	  				echo ' disabled="true"';
	  			}
	  		}else{
	  			echo "<b>After $fixedtime. interval ($fixedtime x $interval)</b><input type=\"checkbox\" name=\"fixedtime[$i]\" id=\"fixedtime[$i]\" value=\"$fixedtime_id\" class=\"checkInput\" ";
	  			if($query_id==$id){
	  				echo " checked=\"checked\" ";
	  			}elseif($query_id!=0){
	  				echo ' disabled="true"';
	  			}
	  		}
	  		echo " />";
  			if($query_id==$id){
	  			echo "<input type=\"hidden\" name=\"unchecked[$i]\" value=\"$fixedtime_id\" />";
	  		}
	  		if($query_id!=0){
	  			$q = new Query($query_id);
	  			$name = $q->getName();
	  			echo "Time assigned for query: $name";
	  		}
	  		echo "<br />";
	  	}
  		echo "<input type=\"hidden\" name=\"fixedtimes\" value=\"".mysql_numrows($fixedTimes)."\" />";
  	}
  }
  ?>  
  <br />
  <!-- <input type="submit" name="submit" value="edit Query"/> -->
  <a class="button" href="#" onclick="this.blur();validateAddQuery('updatequeryform')"><span>Update</span></a>
  <br />
  </form>
  <br />
  </div>
  </div>
  <?php }else{
  			if($locked=="Ended" || $locked=="freezed"){
  				echo "<h1>Research has ended! Cannot edit it anymore</h1>";
  			}else{
  				echo "<h1>Query is locked,someone else is editing it!</h1>";
  			}
  		}
}	
  ?>
  <script type="text/javascript">
	window.onbeforeunload = function(){
		unlock('<?= encrypt($UID) ?>','<?= encrypt($id) ?>',2);
	}
	window.onunload= function(){
		unlock('<?= encrypt($UID) ?>','<?= encrypt($id) ?>',2);
	}
</script>