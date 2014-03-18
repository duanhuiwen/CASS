<?php
if($a->getAuthData('research_owner')==1){
	$name=strip_tags($_POST['name']);
	$descr=strip_tags($_POST['descr']);
	if(is_numeric($_POST['colmet'])){
		$colmet=$_POST['colmet'];
	}
	$UID=$a->getAuthData('uid');
	//Dates
	$startDate = $_POST['startdate'];
	$endDate = $_POST['enddate'];
	// Check date data
	if(validateDatepick($startDate,$endDate)){		
			//Survey times
			$qPerDay = $_POST['surveyPerDay'];
			$queTimeH = $_POST['queryh'];
			$queTimeM = $_POST['querym'];
			//Lets create a new research object
			if(!empty($name) && !empty($descr) && is_numeric($colmet)){
				if(($colmet==0 || $colmet==1) && (isset($_POST['queryh']) && isset($_POST['querym']))){
					$research = new Research();
					if($colmet==0){
						$tmp=$research->createResearch($name, $descr, $colmet, $UID,$startDate,$endDate,$qPerDay);	
					}else{
						$fixedIntH = $_POST['queryIntH'];
						$fixedIntM = $_POST['queryIntM'];
						if(is_numeric($fixedIntH) && $fixedIntH>=0 && $fixedIntH<24 && is_numeric($fixedIntM) && $fixedIntM>=0 && $fixedIntM<60){
							$fixedInt = $fixedIntH.":".$fixedIntM;
							$tmp=$research->createResearch($name, $descr, $colmet, $UID,$startDate,$endDate,$qPerDay);
						}else{
							$error = true;
							echo "<p>Error with data collection method fixed interval.Check given parameters.</p>";
							require_once("../UI/forms/addResearch.php");
						}
					}
				}elseif($colmet==2 || $colmet==3){
					$research = new Research();
					$tmp=$research->createResearch($name, $descr, $colmet, $UID,$startDate,$endDate,$qPerDay);
				}else{
					$error = true;
					echo "<p>Error with data collection method</p>";
					echo var_dump($_POST);
					require_once("../UI/forms/addResearch.php");
				}			
				if(empty($tmp)){
					if(empty($error)){
						echo("<h1>There was a problem creating your research!</h1>");	
					}						
				}else{
					if(($colmet==0) && (isset($_POST['queryh']) && isset($_POST['querym']))){
						$r = new Research($tmp);
	
						for($i=1;$i<=count($queTimeH);$i++){
							$time = $queTimeH[$i].":".$queTimeM[$i];
							if(validateTime($queTimeH[$i],$queTimeM[$i])){
								$r->createQueryTimes(0,$time);
							}else{
								echo "error in Query$i: time $queTimeH[$i]:$queTimeM[$i] is not valid!<br />";
								$error = true;
							}
						}
					}elseif($colmet==1 && isset($_POST['queryh']) && isset($_POST['querym']) && isset($_POST['queryIntH']) && isset($_POST['queryIntM'])){
						$r = new Research($tmp);
						$firsttime = $queTimeH.":".$queTimeM;
						if(validateTime($queTimeH,$queTimeM)){
							$r->createFixed($firsttime,$fixedInt);
						}else{
							echo "time for first query $queTimeH:$queTimeM is not valid!<br />";
							$error = true;
						}
					}
					//Redirect to displayResearch page
					if(!headers_sent()){
		       			header('Location:displayResearch.php?id='.encrypt($tmp));
			    	}else{
			        	echo '<script type="text/javascript">';
			        	echo 'window.location.href="displayResearch.php?id='.encrypt($tmp).'";';
			        	echo '</script>';
			        	echo '<noscript>';
			        	echo '<meta http-equiv="refresh" content="0;url=displayResearch.php?id='.encrypt($tmp).'" />';
			        	echo '</noscript>';
			    	}
				}
			}else{
			$error = true;
			echo "<p>All fields must be filled!</p>";
			require_once("../UI/forms/addResearch.php");
		}
	}else{
		$error = true;
		echo '<div id="invalidform" style="display: block">';
			echo '<div class="errortxt"><p>Date fields are not valid!</p></div>';
		echo '</div>';
		require_once("../UI/forms/addResearch.php");
	}
}else{
	echo "Access denied,you are not allowed to browse this data.";
}
?>