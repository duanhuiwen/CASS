<?php
/*
 * XML Generator. When the phone connects to the server it, it send its Bluetooth id as
 * a GET parameter. This script check based on the bluetooth id the following things:
 * 
 * 1. If there is a respondent connected to the research: the getSubjectByBT returns
 * the user id which is connected the currently ongoing research.
 * 
 * 2. After that the script is checking  
 */
header("Content-type:application/xml");
require("../common/includes.php");
$xml = new XMLAggregator();
//if(isset($_GET['uid']) && isset($_GET['surveyId'])){
if(isset($_GET['uid'])){
	$bt_id = strtoupper($_GET['uid']);
	//get the user id by the bluetooth id
	$s = new Subject();
	$tmp = $s->getSubjectByBT($bt_id);
	if($tmp!=false){
		$rid = $tmp['research_id'];
		$UID = $tmp['UID'];
		$r = new Research($rid);
		$status = $r->isActive();
		$colmet = $r->getCollMethod();
		//get the user id by the bluetooth id
		if($status){
			if($UID!=null){
				if($r->users->isLocalSubject($UID)){
					if($colmet==0){                   //fixed time
						$times = $r->getQueryTimes();
						$r->getSubject($UID);
						$lastAnswer = $r->subjects->getLastAnswer($rid);
						$uts['yesterday'] = strtotime( '-1 days' );
						$yest = date('Y-m-d',$uts['yesterday']);
						$day = $lastAnswer['day'];
						$dayNow = date('Y-m-d');
						$timeH = date('H');
						$timeM = date('i');
						$timeNow = (60*$timeH)+$timeM;
						if($lastAnswer!=false && ($day==$dayNow || $day==$yest)){
							$timel = $lastAnswer['time'];
							$time = explode(":",$timel);
							$time = ($time[0]*60)+$time[1];
							if($dayNow == $day){							
								$qinfo = $r->getQuery($timeNow);
								$qid = $qinfo['qid'];
								$sCount = $qinfo['index'];
								$qt = $qinfo['qtime'];
								$qt = explode(":",$qt);
								$qt = ($qt[0]*60)+$qt[1];
								if($qt<=$time){
									unset($qid);
									$answered = true;
								}
								
							}else{							
								if($day==$yest){
									$numOfQueries = mysql_numrows($times);
									$qtime = mysql_result($times,$numOfQueries-1,'qtime');
									$qtime = explode(':',$qtime);
									$qtime = ($qtime[0]*60)+$qtime[1];
									if($time>=$qtime){
										if($timeNow<$qtime){
											$qid = mysql_result($times,$numOfQueries-1,'query_id');
											$sCount = $numOfQueries-1;
										}
									}else{
										$answered = true;
									}							
								}
							}
						}else{
							$qinfo = $r->getQuery($timeNow);
							$qid = $qinfo['qid'];
							$sCount = $qinfo['index'];
						}	
					}elseif($colmet==1){  //fixed interval
						$r->getSubject($UID);
						$next = $r->subjects->getNextQuery($rid);
						if($next!=false && $next!="SelectError"){
							$qid = $next['query_id'];
							$sCount = $next['fixedtime'];							
						}else{
							$msg = "Query not available yet!";
						}
					}else{  //event contingent
						$query = $r->listChildren();
						$r->getSubject($UID);
						$sCount = $r->subjects->getSurveyCount($rid);
						$qid = mysql_result($query,0,'query_id');
					}
					//writing the xml for mobilephone
					if(!empty($qid)){				
						echo $xml->WriteXml($rid,$qid,$UID,$sCount);
					}else{
						if($answered==true){
							$msg = "You have already answered this query today at $timel. Now".date('H:i')."!";
							echo XMLMessage($msg,$UID,$rid);
						}else{
							if(empty($msg)){
								$msg = "An unknown error has occured!";
							}
							echo XMLMessage($msg,$UID,$rid);
						}
					}				
				}else{
					$msg = "You are not a subject in this research!";
					echo XMLMessage($msg,$UID,$rid);
				}
			}else{
				$msg = "User not identified!";
				$UID = "unknown";
				echo XMLMessage($msg,$UID,$rid);
			}
		}else{
			$msg = "Research is not active at the moment!";
			echo XMLMessage($msg,$UID,$rid);
		}
	}else{
		$msg = "Unknown user or the research has ended.";
		echo XMLMessage($msg,0,0);
	}
}


?>