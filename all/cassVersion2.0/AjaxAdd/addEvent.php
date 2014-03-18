<?php
// This script make the questions. You can add the question, the type of it, the category
	require_once("../common/includes.php");
	
	$questionText = $_GET['question'];		// Gets the string of the question
	$questionType = $_GET['questionType'];	// Gets the type, the type number of the question
	$queryID =$_GET['queryid'];				// Gets the id of the query
	$questionID=$_GET['questionid'];		// Gets the id of the question
	$category = $_GET['questionCat'];		// Gets the category number of the questions
	$items = $_GET['item'];					/* Gets the item number of the question.
	                       					 * Used in the draggable and droppable
	                       					 * jQuery UI javascript functions.
	                       					 */
	
	if(isset($queryID)){
			$question = new Question($queryID);
			if($question->createQuestion($questionText, $questionType, $category)!=false){
				if($questionType == 4){
					$num = $_GET['num'];
					for($i=1;$i<=$num;$i++){
						$option = "option".$i;
						if($question->addOption($_GET[$option],-1)==false){
							echo false;
						}
					}
				}elseif($questionType == 5){
					$num = $_GET['num'];
					for($i=1;$i<=$num;$i++){
						$option = "option".$i;
						$super = "superof".$i;
						if($question->addOption($_GET[$option],$_GET[$super])==false){
							echo false;
						}
					}
				}elseif($questionType == 9){
						for($i=1;$i<=5;$i++){
							$option = "slider".$i;
							if($question->addOption($_GET[$option],-1)==false){
								echo false;
							}
						}
				}elseif($questionType == 2){
						for($i=1;$i<=2;$i++){
							$option = "open".$i;
							if($question->addOption($_GET[$option],-1)==false){
								echo false;
							}
						}
				}elseif($questionType == 10){
					$num = $_GET['num'];
					for($i=1;$i<=$num;$i++){
						$option = "option".$i;
						if($question->addOption($_GET[$option],-1)==false){
							echo false;
						}
					}
				}
				echo listEvents($queryID);
			}else{
				echo 0;
			}
		}
		//Goes only here if a question is modified.Makes a new one and removes the old one
		if(isset($questionID)){
			$question = new Question(0,$questionID);
			$queryID = $question->getOwner();
			$number = $question->getNumber();
			$quest = new Question($queryID);
			
			if($quest->createQuestion($questionText, $questionType, $category,$number)){
				if($question->rmQuestion()){
					$quest->updateQuestion($questionID);
				}
				if($questionType == 4){
					$num = $_GET['num'];
					for($i=1;$i<=$num;$i++){
						$option = "option".$i;
						$quest->addOption($_GET[$option],-1);
					}
				}elseif($questionType == 5){
					$num = $_GET['num'];
					for($i=1;$i<=$num;$i++){
						$option = "option".$i;
						$super = "superof".$i;
						$supa = $_GET[$super];
						/*if($supa==0){
							//$supa = -1;
						}*/
						$quest->addOption($_GET[$option],$supa);
					}
				}elseif($questionType == 9){
					for($i=1;$i<=5;$i++){
						$option = "slider".$i;
						$quest->addOption($_GET[$option],-1);
					}
				}elseif($questionType == 2){
					for($i=1;$i<=2;$i++){
						$option = "open".$i;
						$quest->addOption($_GET[$option],-1);
					}
				}elseif($questionType == 10){
					$num = $_GET['num'];
					for($i=1;$i<=$num;$i++){
						$option = "option".$i;
						$quest->addOption($_GET[$option],-1);
					}
				}
				echo listEvents($queryID);
			}
		}
		
		
		if(isset($_GET['id']) && isset($_GET['copy'])){
			$copy = $_GET['copy'];
			$item = $_GET['item'];
			if(count($copy)<2 && count($copy)>0){
				$q = new Question(0,$_GET['id']);
				$questionText = $q->getQuestionText();
				$questionType = $q->getQuestionType();
				$category = $q->getCategory();
				$number = $_GET['num'];
				//$f = new FileIOHandler();
				if(isset($_GET['copyto'])){			
					$qrID = $_GET['copyto'];
				}
				if(isset($qrID)){
					$quest = new Question($qrID);
					$id = $quest->createQuestion($questionText, $questionType, $category,$number);				
					if($id!=false){
						if($questionType == 4 || $questionType == 5 || $questionType == 9 || $questionType == 2 || $questionType == 10){
							$options = $q->getOptions();
							for($i=0;$i<count($options);$i++){
								$quest->addOption($options[$i]['option'],$options[$i]['super_of']);
							}
						}
						$query = new Query($qrID);
						$childs = $query->listChildren();
						$nums = mysql_numrows($childs);
						for($k=0;$k<$nums;$k++){
							$questId = mysql_result($childs,$k,'question_id');
							$n = mysql_result($childs,$k,'number');
							if($n>=$number && $questId!=$id){
								$question = new Question(0,$questId);
								$question->setNumber($n+1);
							}
						}
						echo listEvents($qrID);
					}
				}
			}
		}elseif(isset($items)){
			
			foreach($items as $i=>$quid){
				$q = new Question(0,$quid);
				$q->setNumber($i+1);
			}
		}
		
		if(isset($_GET['copyto']) && isset($_GET['copyfrom'])){
			$copyto= $_GET['copyto'];
			$copyfrom = $_GET['copyfrom'];
			if(is_numeric($copyto) && is_numeric($copyfrom)){
				$qr = new Query($copyfrom);
				if($qr->copyQuery($copyto)){
					echo listEvents($copyto);
				}
			}
		}
?>