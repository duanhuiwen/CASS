<?php
/*
 * This file is for modifying already existing question in the query. It prints all the options
 * of the question. If the Modify button is pressed the user changes the actual question.
 */
/*
 * Checks the type of the question. If it is the same as the input number then it returns
 * checked. This will make the radio button in the list checked.
 */  
function checkSelectedType($num, $question){
	if($question->getQuestionType()==$num){
		echo("checked");
	}
}

// This function is not used.
function checkSuper($opts){
	$num=count($opts);
	$super= false;
	for($i=0; $i<$num; $i++){
		if($opts[$i]['super_of']!=-1){
			$super= true;
		}
	}
	if($super){
		echo("<input type=\"radio\" name=\"super\" id=\"super\" value=\"1\" checked />Yes <br />");
		echo("<input type=\"radio\" name=\"super\" id=\"super\" value=\"0\" />No <br />");
	}else{
		echo("<input type=\"radio\" name=\"super\" id=\"super\" value=\"1\" />Yes <br />");
		echo("<input type=\"radio\" name=\"super\" id=\"super\" value=\"0\" checked />No <br />");
	}
	return $super;
}

include("../common/includes.php");
$temp1=$_GET['id'];
$temp1=substr($temp1, 5);
$temp = explode(";",$temp1);
$qid=$temp[0];
$queryID=$temp[1];
if($qid!=null){
	$question = new Question(0, $qid);
	$type = $question->getQuestionType();
	if($type==2 || $type==4 || $type==5 || $type==9 || $type==10){
		$opts=$question->getOptions();
	}
	if($opts){
		$numOfOpts = count($opts);
	}else{
		$numOfOpts = 0;
	} 
}



?>
<div id="addEvent" class="editbox">
	<!-- <div id="button" name="cancel" value="X" onClick="cancelAdd();cursor_default()" class="X" onMouseOver="cursor_button()" onMouseOut="cursor_default()" >&nbsp;x&nbsp;</div> -->
	<div id="button" name="cancel" value="X" class="X"><a class="button" href="#" onclick="this.blur();cancelAdd();fadeaway()"><span>X</span></a></div>
	<input type="text" name="question" id="question" value="<?php echo($question->getQuestionText()); ?>" maxlength="255" />
    <div class="q_general"> Category <input type="text" size="2" maxlength="2"
    	name="Category" id="category" class="textedit" value="<?php echo($question->getCategory()); ?>"/><br /></div>
   	<!--<span class="q_general2">Number: <?php echo($question->getNumber()); ?><br /></span> -->
   	<div id="q_topleft">
		<br />
		<input type="radio" name="type" id="type" value="1" onClick="hideElement('q_topright');hideElement('q_middle');hideElement('q_middle2')" <?php checkSelectedType(1, $question) ?> class="checkInput" /> Open text<br />
		<input type="radio" name="type" id="type" value="2" onClick="hideElement('q_topright');hideElement('q_middle');showElement('q_middle2');createOpenNumberOptions(this.value)" <?php checkSelectedType(2, $question) ?> class="checkInput" /> Open number <br />
		<input type="radio" name="type" id="type" value="3" onClick="hideElement('q_topright');hideElement('q_middle');hideElement('q_middle2')" <?php checkSelectedType(3, $question) ?> class="checkInput" /> Sound <br />
		<input type="radio" name="type" id="type" value="4" onClick="checkIfCreateOptionsNeeded(this.value);hideElement('q_middle2');showElement('q_topright');showElement('q_middle');showElement('q_element')" <?php checkSelectedType(4, $question) ?> <?php checkSelectedType(10, $question) ?> class="checkInput"/> Multiple choice <br />
		<input type="radio" name="type" id="type" value="5" onClick="checkIfCreateOptionsNeeded(this.value);hideElement('q_middle2');showElement('q_topright');showElement('q_middle');showElement('q_element')" <?php checkSelectedType(5, $question) ?> class="checkInput" /> Super <br />
		<input type="radio" name="type" id="type" value="6" onClick="hideElement('q_topright');hideElement('q_middle');hideElement('q_middle2')" <?php checkSelectedType(6, $question) ?>  class="checkInput"/> Comment <br />
		<input type="radio" name="type" id="type" value="7" onClick="hideElement('q_topright');hideElement('q_middle');hideElement('q_middle2')" <?php checkSelectedType(7, $question) ?> class="checkInput"/> Photo <br />
		<input type="radio" name="type" id="type" value="8" onClick="hideElement('q_topright');hideElement('q_middle');hideElement('q_middle2')" <?php checkSelectedType(8, $question) ?> class="checkInput"/> Video <br />
		<input type="radio" name="type" id="type" value="9" onClick="hideElement('q_topright');hideElement('q_middle');showElement('q_middle2');createSliderOptions(this.value)" <?php checkSelectedType(9, $question) ?> class="checkInput"/> Slider <br />
		<br />
	</div>
    	
    <div id="q_topright">
    	<?php if($type==4 || $type==5 || $type==10){ ?>
    			Set number of choices:<br /> &nbsp;<input type="text" id="num" name="num" value="<?php echo("$numOfOpts") ?>" size="2" maxlength="2" class="textedit" onKeyUp="options(this.value)" /><br />  		 		
    	<?php }else{ //end if ?>
				<div id="q_element" style="display: none">
					<p>Set number of choices:<br /> &nbsp;<input type="text" id="num" name="num" value="" size="2" maxlength="2" class="textedit" onKeyUp="options(this.value)"/></p>
				</div>
    	 <?php }//end of else ?>
    </div>
		
    	<?php if($type==4 || $type==5 || $type==10){
		    	echo "<div id=\"q_middle\">";
    			if($type==4 || $type==10){
    				echo "<div id=\"multipleCheckBox\" name=\"mulCheckBoxDiv\"><input type=\"checkbox\" id=\"mulCheckBoxInput\" name=\"mulCheckBoxInput\" ";
    				if($type==10){
    					echo "checked=\"checked\"";
    				}
    				echo "/> multi ans.</div>";
    				echo "<br />Options<br />";
    			}else{
    				echo "<br />Options";
    			}
    			for($i=0; $i<$numOfOpts; $i++){
    				$rown = $i+1;
    				$curropt = $opts[$i]['option'];
    				echo "<div id=\"qoption$rown\" name=\"qoption\">";
    				echo("<br />".$rown .".<input type=\"text\" value=\"$curropt\" name=\"choice\" id=\"option$rown\" class=\"textedit\" size=\"17\" />");    		
    				echo "<div id=\"soption$rown\" name=\"soption\">";
    				if($type==5){
    					$supnum = $opts[$i]['super_of'];
    					echo("Super of: <input type=\"text\" value=\"$supnum\" name=\"choice\" id=\"superof$rown\" class=\"textedit\" size=\"2\" maxlength=\"2\" />");
    				}
    				echo "</div>";
    				echo "</div>";
    			}
    		}else{
    			echo "<div id=\"q_middle\" style=\"display:none\">";
    			echo "Choice text<br />";
    		}
    	?>
</div>
<div id="q_middle2">
   	<?php if($type==9){
    		for($i=0; $i<5; $i++){
    			$curropt[$i] =array(
    			'opt' => $opts[$i]['option'],					    			
    			);
    		}
    		echo("Min label: <input type=\"text\" value=\"".$curropt[0]['opt']."\" name=\"sliderChoice\" id=\"slider1\" class=\"textedit\" maxlength=\"50\" size=\"15\" /><br />");
    		echo("Min value: <input type=\"text\" value=\"".$curropt[1]['opt']."\" name=\"sliderChoice\" id=\"slider2\" class=\"textedit\" maxlength=\"3\" size=\"3\" /><br />");
    		echo("<br />Max label: <input type=\"text\" value=\"".$curropt[2]['opt']."\" name=\"sliderChoice\" id=\"slider3\" class=\"textedit\" maxlength=\"50\" size=\"15\" /><br />");
    		echo("Max value: <input type=\"text\" value=\"".$curropt[3]['opt']."\" name=\"sliderChoice\" id=\"slider4\" class=\"textedit\" maxlength=\"3\" size=\"3\" /><br />");
    		echo("<br />Scale is 1.<input type=\"hidden\" value=\"".$curropt[4]['opt']."\" name=\"sliderChoice\" id=\"slider5\" class=\"textedit\" maxlength=\"3\" size=\"3\" /><br />"); //type="text"
    	} 
		if($type==2){
    		for($i=0; $i<$numOfOpts; $i++){
    			$curropt[$i] =array(
    			'opt' => $opts[$i]['option'],					    			
    			);
    		}
    		echo("Min: <input type=\"text\" value=\"".$curropt[0]['opt']."\" name=\"openChoice\" id=\"open1\" class=\"textedit\" maxlength=\"3\" size=\"3\" /><br />");
    		echo("Max: <input type=\"text\" value=\"".$curropt[1]['opt']."\" name=\"openChoice\" id=\"open2\" class=\"textedit\" maxlength=\"3\" size=\"3\" /><br />");
    	}
    ?>
    	<br />
    	<br />
</div>
		
<div id="bottom">
	<div class=bottomButtons>
		<div class="modifybut"><a class="button" href="#" onclick="this.blur();validateAddque('<?php echo("$temp1") ?>',1)"><span>Modify</span></a></div> 
		<div class="removebut"><a class="button" href="#" onclick="this.blur();removeEvent('<?php echo("$temp1") ?>')"><span>Remove</span></a></div>
	</div>
</div>
</div>