<!-- <script type="text/javascript"  src="../AjaxAdd/functions.js"></script> -->
<script type="text/javascript">
	window.onload = function(){
		datepick();
	}
</script>
<?php if($research->users->isLocalAdmin($UID)){
		$locked = $research->isLocked(); 
		 if($locked==false || $locked==$UID){
		 	$research->setLocked($UID);
		 	if($_GET['error']==1){
		 		echo "<p>Fields where not all valid!</p>";
		 	}elseif($_GET['error']==2){
		 		echo "<p>Date fields where not all valid!</p>";
		 	}
?>
	<div id="addsubsdiv">
		<div class="descrheader">
			<div class="headertext"><h1>Edit research</h1><hr /></div>
		</div>
		<div class="addresearchcontent">
		<div id="invalidform"><div class="errortxt"></div></div>
  			<form id="editresearchform" action="./editResearch_act.php" method="post" enctype="multipart/form-data">
  				<p><h2>Research name</h2>
  				   <input type="text" id="name" name="name" value="<?php echo"$rname"; ?>" size="40" maxlength="40"/><div class="rnameerror"></div>
  				</p>
  				<p><h2>Description</h2>
  				   <input type="text" id="descr" name="descr" value="<?php echo"$desc"; ?>" size="40" maxlength="120"/><div class="rdescrerror"></div>
  				</p>
	  	<br />
	  	<h2>Time interval</h2>
	  	<h2>Select time range for research</h2>
	  		<input type="text" id="start" name="startdate" size="13" value="<?php echo"$start"; ?>" /> 
	  		 -  <input type="text" id="end" name="enddate" size="13" value="<?php echo"$end"; ?>" /> 
	  		 <div class="rdateerror"></div>
 
  		<div class="colmetselect"><h2>Data collection method</h2> 
  	
		 	<select name="colmet" id="colmet" size="1" onChange="checkColmet()">
			    <option value="0" <?php if($method==0){echo"selected=\"selected\"";} ?>>Fixed time</option>
			    <option value="1" <?php if($method==1){echo"selected=\"selected\"";} ?>>Fixed interval</option>
			    <option value="2" <?php if($method==2){echo"selected=\"selected\"";} ?>>Event Contingent</option>
			    <!-- <option value="3" <?php //if($method==3){echo"selected=\"selected\"";} ?>>Random</option>  -->
		  </select>
		  </div>
  <div id="queryAmount"
  	<?php if($method==2){echo"style=\"display:none\"";} ?>
  	><h2>Amount of queries per day</h2>
  <div id="queryAmountInput"
  <?php if($method==2){echo"style=\"display:none\"";} ?>
  ><input type="text" name="surveyPerDay" id="surveyPerDay" value="<?php echo $qPerDay; ?>" size="2" maxlength="1" onKeyUp="createQueryTimes(this.value)" /></div></div>
  <input type="hidden" name="rid" value="<?php echo encrypt($id); ?>" />
  <div name="addr_survey" id="addr_survey">
  <?php if($method==0){ 
  			echo "<h2>Set times for queries</h2>";
  			for($i=1;$i<=mysql_numrows($queryTimes);$i++){
  				$j=$i-1;
  				$q = mysql_result($queryTimes,$j,"qtime");
  				$qid = mysql_result($queryTimes,$j,"qtime_id");
  				$query_id = mysql_result($queryTimes,$j,"query_id");
  				$q = explode(":",$q);
  				$qh = $q[0];
  				$qm = $q[1];  				
  				echo "<div id=\"qtimediv$i\" name=\"qtimediv\">$i.Query: Hour:<select name=\"queryh[$i]\">";
  				for($h=0;$h<24;$h++){
  					if($h==$qh){
  						echo "<option value=\"$qh\" selected=\"selected\">$qh</option>";
  					}else{
						if($h<10){
							echo "<option value=\"0$h\">0$h</option>";
						}else{
							echo "<option value=\"$h\">$h</option>";
						}
  					}
				}
				echo "</select>";
				echo "Min:<select name=\"querym[$i]\">";
  				for($m=0;$m<60;$m++){
  					if($m==$qm){
  						echo "<option value=\"$qm\" selected=\"selected\">$qm</option>";
  					}else{
						if($m<10){
							echo "<option value=\"0$m\">0$m</option>";
						}else{
							echo "<option value=\"$m\">$m</option>";
						}
  					}
				}
				echo "</select><br />";
				echo "<input type=\"hidden\" name=\"querytid[$i]\" value=\"".encrypt($qid)."\" />";
				echo "<input type=\"hidden\" name=\"query_id[$i]\" value=\"".encrypt($query_id)."\" />";
				echo "</div>";
  			}
  	}
  ?>
  </div>
  <div id="addr_survey2" name="addr_survey2">
  	<? if($method==1){
  			$firsttime = $research->getFixedFirsttime();
  			$interval = $research->getFixedInterval();
  			$first = explode(":",$firsttime);
  			$int = explode(":",$interval);
  			$inth = $int[0];
  			$intm = $int[1]; 
  			$firsth = $first[0];
  			$firstm = $first[1];  
  			echo "<b>Set Time for first query:</b> Hour: <select name=\"queryh\">";

			for($h=0;$h<24;$h++){
				if($h==$firsth){
  					echo "<option value=\"$firsth\" selected=\"selected\">$firsth</option>";
  				}else{
					if($h<10){
						echo "<option value=\"0$h\">0$h</option>";
					}else{
						echo "<option value=\"$h\">$h</option>";
					}
  				}
			}
			echo "</select>";
			echo " Min: <select name=\"querym\">";
  			for($m=0;$m<60;$m++){
  				if($m==$firstm){
  					echo "<option value=\"$firstm\" selected=\"selected\">$firstm</option>";
  				}else{
					if($m<10){
						echo "<option value=\"0$m\">0$m</option>";
					}else{
						echo "<option value=\"$m\">$m</option>";
					}
  				}
			}
			echo "</select><br /><br />";
			echo "<b>Give interval:</b> Hours: <select name=\"queryIntH\">";
			$biggestInt = floor(24/$qPerDay);
			for($ih=0;$ih<$biggestInt;$ih++){
				if($ih==$inth){
  					echo "<option value=\"$inth\" selected=\"selected\">$inth</option>";
  				}else{
					if($ih<10){
						echo "<option value=\"0$ih\">0$ih</option>";
					}else{
						echo "<option value=\"$ih\">$ih</option>";
					}
  				}
			}
			echo "</select>";
			echo " Minutes: <select name=\"queryIntM\">";
			for($im=0;$im<60;$im++){
				if($im==$intm){
  					echo "<option value=\"$intm\" selected=\"selected\">$intm</option>";
  				}else{
					if($im<10){
						echo "<option value=\"0$im\">0$im</option>";
					}else{
						echo "<option value=\"$im\">$im</option>";
					}
  				}
			}
			echo "</select><br />";
  		} ?>
  </div>
  <p>
  	<a class="button" href="#" onclick="this.blur();validateAddResearch('editresearchform')"><span>Update research</span></a>
  	<a class="button" href="./displayResearch.php?id=<?php echo encrypt($id); ?>"><span>Cancel</span></a>
  </p>
  
  </form>
  </div>
  </div>
  <?php }else{
  			if(is_numeric($locked)){ 
  				echo "<h1>Research is locked,someone else is editing it!</h1>";
  			}elseif($locked=="Ended" || $locked=="freezed"){
  				echo "<h1>Research has ended! Cannot edit it anymore</h1>";
  			}
  		}
}
//encoded info for javascript
$enUID = encrypt($UID);
$enId = encrypt($id);

  ?>
<script type="text/javascript">
	window.onbeforeunload = function(){
		unlock('<?= $enUID ?>','<?= $enId ?>',1);
	}
	window.onunload = function(){
		unlock('<?= $enUID ?>','<?= $enId ?>',1);
	}
</script>