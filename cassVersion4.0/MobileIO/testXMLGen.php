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

require("../common/includes.php");

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
		
						$times = $r->getQueryTimes();
						$r->getSubject($UID);
						$lastAnswer = $r->subjects->getLastAnswer($rid);
$uts['yesterday'] = strtotime( '-1 days' );
$dayNow = date('Y-m-d');
						$timeH = date('H');
						$timeM = date('i');
						$timeNow = (60*$timeH)+$timeM;
						$yest = date('Y-m-d',$uts['yesterday']);
						//echo $r->subjects;
						//echo gettype($r->subjects);
						$day = $lastAnswer['day'];
						//echo $day;
//echo "yest:".$yest ."<br>";
//if($lastAnswer != false)
//echo ($lastAnswer[time])."last answered is not qual to false<br>" ; if($day==$dayNow) echo "day==dayNow";if( $day==$yest) echo "day==yest";
if($lastAnswer!=false && ($day==$dayNow || $day==$yest)){
echo 'comes into first if';
$timel = $lastAnswer['time'];
							$time = explode(":",$timel);
							//echo $timel;
							//echo 'time[]:' .$time[0].' '.$time[1].' '.$time[2];
							$time = ($time[0]*60)+$time[1];
							if($dayNow == $day){	echo 'daynow ==day<br>';						
								$qinfo = $r->getQuery($timeNow);
								echo $timeNow.'<br>';
								$qid = $qinfo['qid'];
								$sCount = $qinfo['index'];
								$qt = $qinfo['qtime'];
								//echo 'qt[0]'.$qt[0];
								echo "qid:".$qid.'<br>';
								echo "scount:".$sCount.'<br>';
								echo "qt with dots: ".$qt.'<br>';
								$qt = explode(":",$qt);
								$qt = ($qt[0]*60)+$qt[1];
								echo "qt without dots: ".$qt.'<br>';
								echo '<br>last answered time '. $time;
								if($qt<=$time){
									unset($qid);
									$answered = true;
									echo 'comes into second if';
								}
								
								
}

						}}}
?>