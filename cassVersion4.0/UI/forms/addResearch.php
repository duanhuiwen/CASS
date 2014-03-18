<?php if($a->getAuthData('research_owner')==1){ //Check user level ?>
<script type="text/javascript">
	window.onload = function(){
		datepick();
	}
	function clearInput(id){
		document.getElementById(id).value = "";
	}
</script>
<div id="addresearchdiv">
  	<form  name="newresearchform" id="newresearchform" action="./addResearch_act.php" method="post" enctype="multipart/form-data">
	  <div class="descrheader"><div class="headertext"><div id="help" onclick="openhelp('addres');">?</div><h1>Add new research</h1><hr /></div></div>
	  <div class="addresearchcontent">
		  	<div id="invalidform"><div class="errortxt"></div></div>
		  	<br />
		  	<h2>Research name</h2>
		  	<input type="text" name="name" id="name" value="The name or designation of the research" size="40" maxlength="40" onClick="clearInput('name')" onFocus="clearInput('name')" /><div class="rnameerror"></div>
		  	<br />
		  	<h2>Description</h2>
		  	<input type="text" name="descr" id="descr" value="A brief description, visible to anyone" size="40" maxlength="150" onClick="clearInput('descr')" onFocus="clearInput('descr')" /><div class="rdescrerror"></div>
		  	<br />
		  	<h2>Select time range for research</h2>
			<!-- <div id="ui-datepicker">  -->
		  		<input type="text" id="start" name="startdate" value="select start date" size="13" />
		  		 -  <input type="text" id="end" name="enddate" value="select end date" size="13" maxlength="10" />
		  	<div class="rdateerror"></div>
		  	<!-- </div> -->
		 
		  <div class="colmetselect">
		  	<h2>Data collection method</h2>
			  <select name="colmet" id="colmet" size="1" onChange="checkColmet()">
			    <option value="0">Fixed time</option>
			    <option value="1">Fixed interval</option>
			    <option value="2">Event Contingent</option>
			    <!-- <option value="3">Random</option>  Not implemented yet! -->
			  </select>
		  </div>
		  <div id="queryAmount"><h2>Amount of queries per day</h2><div id="queryAmountInput"><input type="text" name="surveyPerDay" id="surveyPerDay" value="" size="2" maxlength="1" onKeyUp="createQueryTimes(this.value)" /></div></div>
		  <div name="addr_survey" id="addr_survey"></div>
		  <div name="addr_survey2" id="addr_survey2"></div>
		  <br />
		  <!-- <p><input type="submit" name="submit" value="Add Research"/></p> -->
		  <a class="button" href="#" onclick="this.blur();validateAddResearch('newresearchform')"><span>Add</span></a>
	  </div>	  
	</form>
  </div>
<?php } ?>