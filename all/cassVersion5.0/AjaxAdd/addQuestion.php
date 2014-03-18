<!-- This HTML part draws the actual panel to add a new question -->
<script>  	
	setTimeout('nothing()',500);
	function sancel(){
		$('#ui-tabs-7').html(''); //  was ui-tabs-7
	}
	function selector(index){
		$("#edit").tabs("select", index);
	}
	function nothing(){
		$('#addEvent').css('visibility','visible');
	}
</script>
	<?php 
	include("../common/includes.php");
	$qid=$_GET['id']; 
	?>
	
	<h3>Add new question:</h3>
	<div id="addEvent" class="editbox" style="visibility:hidden">
		
			<!-- <div id="button" name="cancel" value="X" onClick="sancel()" class="X" >&nbsp;x&nbsp;</div> -->
			<div id="button" name="cancel" value="X" class="X" ><a class="button" href="#" onclick="this.blur();sancel()"><span>X</span></a></div>
    		<input type="text" id="question" name="question" value="" size="25" maxlength="255" />
    		<div class="q_general">Category <input type="text" size="3" name="Category" id="category" class="textedit"/><br /></div>
    		<div id="q_topleft">
	    		<br />
				<input type="radio" name="type" id="type" value="1" onClick="hideElement('q_topright');hideElement('q_middle');hideElement('q_middle2')" class="checkInput" /> Open text<br />
				<input type="radio" name="type" id="type" value="2" onClick="hideElement('q_topright');hideElement('q_middle');showElement('q_middle2');createOpenNumberOptions(this.value)" class="checkInput" /> Open number <br />
				<input type="radio" name="type" id="type" value="3" onClick="hideElement('q_topright');hideElement('q_middle');hideElement('q_middle2')" class="checkInput" /> Sound <br />
				<input type="radio" name="type" id="type" value="4" onClick="showElement('q_topright');showElement('q_middle');hideElement('q_middle2');checkIfCreateOptionsNeeded(this.value)" class="checkInput" /> Multiple choice <br />
				<input type="radio" name="type" id="type" value="5" onClick="showElement('q_topright');showElement('q_middle');hideElement('q_middle2');checkIfCreateOptionsNeeded(this.value)" class="checkInput" /> Super <br />
				<input type="radio" name="type" id="type" value="6" onClick="hideElement('q_topright');hideElement('q_middle');hideElement('q_middle2')" class="checkInput" /> Comment <br />
				<input type="radio" name="type" id="type" value="7" onClick="hideElement('q_topright');hideElement('q_middle');hideElement('q_middle2')" class="checkInput" /> Photo <br />
				<input type="radio" name="type" id="type" value="8" onClick="hideElement('q_topright');hideElement('q_middle');hideElement('q_middle2')" class="checkInput" /> Video <br />
				<input type="radio" name="type" id="type" value="9" onClick="hideElement('q_topright');hideElement('q_middle');showElement('q_middle2');createSliderOptions(this.value)" class="checkInput" /> Slider <br />
				<br />
			</div>
    		
    		<div id="q_topright" style="display:none">
    		 		Set number of choices:<br /> &nbsp;<input type="text" id="num" name="numOfOpt" value="" size="2" maxlength="4" class="textedit" onKeyUp="options(this.value)"/><br />
    		</div>
    		
			<div id="q_middle"></div>
			
    		<div id="q_middle2"></div>
			
		<br />
    	<div id="bottom">
	    	<div class="bottomButtons">
				<a class="button" href="#" onclick="this.blur();validateAddque(<?php echo("$qid"); ?>,0)"><span>Add</span></a>
			</div>
		</div> 
	</div>