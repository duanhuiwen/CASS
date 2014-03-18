<?php
/*require_once("../common/customError.php");
error_reporting(0);
set_error_handler('errorHandler');
*/

if($r->users->isLocalAdmin($UID)){
	$name = strip_tags($_POST['name']);
	$researchID = strip_tags($id);
	$qtime = $_POST['qtime'];
	$querytimes = strip_tags($_POST['querytimes']);
	$fixedtime = $_POST['fixedtime'];
	$fixedtimes = strip_tags($_POST['fixedtimes']);
	//Let's create a new research object!
	$r = new Research($researchID);
	$colmet = $r->getCollMethod();
	$qPerDay = $r->getQueriesPerDay();
	$queries = mysql_numrows($r->listChildren());
	if((($colmet==0 || $colmet==1) && $qPerDay==$queries) || (($colmet==2 || $colmet==3) && $queries>0)){
		echo "<h1>Cannot insert more queries,these queries will never be sent!</h1>";
	}else{
		$query = new Query();
		$tmp=$query->createQuery($name, $researchID);
	}
		
	if($tmp==false){	
		echo("<h1>There was a problem creating your query!</h1>");	
	}else{
		if($colmet==0){
			for($i=0;$i<$querytimes;$i++){
				if(!empty($qtime[$i])){
					$r->setQueryTimesQueryId($qtime[$i],$tmp);
				}
			}
		}elseif($colmet==1){
			for($i=0;$i<$fixedtimes;$i++){
				if(!empty($fixedtime[$i])){
					$r->setFixedTimesQueryId($fixedtime[$i],$tmp);
				}
			}
		}
		
    	if(!headers_sent()){
       		header('Location:manipQuery.php?id='.encrypt($tmp));
    	}else{
        	echo '<script type="text/javascript">';
        	echo 'window.location.href="manipQuery.php?id='.encrypt($tmp).'";';
        	echo '</script>';
        	echo '<noscript>';
        	echo '<meta http-equiv="refresh" content="0;url=manipQuery.php?id='.encrypt($tmp).'" />';
        	echo '</noscript>';
    	}
	}
}		
?>