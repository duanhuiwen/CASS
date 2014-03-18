<?php
/*
 * This file updates the existing queries in a research. It adds the new times to the research. 
 */
if($res->users->isLocalAdmin($UID)){
	$name=strip_tags($_POST['name']);
	$qtime = $_POST['qtime'];
	$querytimes = strip_tags($_POST['querytimes']);
	$fixedtime = $_POST['fixedtime'];
	$fixedtimes = strip_tags($_POST['fixedtimes']);
	$rmQtime = $_POST['unchecked'];
	$colmet = $res->getCollMethod();
	
	$tmp=$query->setName($name);

		if($tmp==false){	
			echo("<h1>There was a problem updating your query!</h1>");
		}else{
			if($colmet==0){		
				if($querytimes>0){
					for($i=0;$i<$querytimes;$i++){
						if(!empty($qtime[$i])){
							$res->setQueryTimesQueryId($qtime[$i],$id);
						}
						if(!empty($rmQtime[$i]) && ($qtime[$i]!=$rmQtime[$i])){
							$res->setQueryTimesQueryId($rmQtime[$i],0);
						}
					}
				}
			}elseif($colmet==1){
				if($fixedtimes>0){
					for($i=0;$i<$fixedtimes;$i++){
						if(!empty($fixedtime[$i])){
							$res->setFixedTimesQueryId($fixedtime[$i],$id);
						}
						if(!empty($rmQtime[$i]) && ($fixedtime[$i]!=$rmQtime[$i])){
							$res->setFixedTimesQueryId($rmQtime[$i],0);
						}
					}
				}
			}
			
			$u->unlock();
	    	if(!headers_sent()){
	        	header('Location:displayQuery.php?id='.encrypt($id));
	    	}else{
		        echo '<script type="text/javascript">';
		        echo 'window.location.href="displayQuery.php?id='.encrypt($id).'";';
		        echo '</script>';
		        echo '<noscript>';
		        echo '<meta http-equiv="refresh" content="0;url=displayQuery.php?id='.encrypt($id).'" />';
		        echo '</noscript>';
	    	}
		}
}
?>