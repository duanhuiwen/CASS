<!-- This file shows all the researches that the researcher made him- or herself. It uses 
jQuery UI to show the researches in accordion. If the user clicks the research it shows
all the queries in the research. After clicking a query it show the question list. Clicking
triggers the getCopies function in functions.js which calls then the 
AjaxAdd/copyQuestionList.php. -->
<script>
function showAccordion(){
	$("#accordionCopy").css('visibility','visible').accordion({autoHeight: false, collapsible: true });    
}
setTimeout("showAccordion()",500);
</script>
<?php
//Login form generation function include
require_once("../common/auth_loginf.php"); 
require_once("../common/auth_start.php");

if($a->checkAuth()){ ///Start secured content
	require_once("../common/includes.php"); //Class includes
	$UID=$a->getAuthData('uid'); //get user ID
	$u = new User($UID);
	
	if($a->getAuthData('su_admin')==0 && $a->getAuthData('research_owner')==0 && $u->hasRightToLoginIn()==false){
		echo "Access denied! Subjects can't login into the admin tool.";
	}else{
		$sql=new ResearchSQLQueryer();
		$researchList=$sql->listResearchByUser($UID);
		$num = mysql_numrows($researchList);
		if($num>0){
			$html = "<ul id=\"accordionCopy\" style=\"visibility:hidden\">";
			while($row = mysql_fetch_array($researchList)){
				$rid = $row['research_id'];
				$rname = $row['research_name'];
				$admin = $row['admin'];
				if($admin==1){
					$colmet = $sql->getCollMethod($rid);
					if ($colmet == 2){
						$ques = $sql->getQueries($rid);
					}elseif($colmet == 1){
						$ques = $sql->getQueries1($rid);						
					}elseif($colmet == 0){
						$ques = $sql->getQueries0($rid);
					}
					$qnum = mysql_numrows($ques);
					$html .="<h3><a href=\"#\" style=\"color:black; line-height: 1em\">$rname</a></h3>";
					$html .="<div>";
					if($qnum>0){				
						while($rw = mysql_fetch_array($ques)){
							$html .="<div class=\"copyQuery\" id=\"copy".$rw['query_id']."\" onClick=\"getCopies(".$rw['query_id'].",".$_GET['copyto'].")\">".$rw['name']."</div>";
						}					
					}else{
						$html .= "<div>No queries</div>";
					}
					$html .="</div>";
				}
			}
			$html .= "</ul>";
			echo $html;
		}
	}
}
?>