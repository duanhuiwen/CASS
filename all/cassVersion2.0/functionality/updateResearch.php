<?php
if($res->users->isLocalAdmin($UID)){
		$name=strip_tags($_POST['name']);
		$descr=strip_tags($_POST['descr']);
		$colmet=strip_tags($_POST['colmet']);
		$rid = strip_tags($_POST['rid']);
		$UID=$a->getAuthData('uid');
		/*$sd = $_POST['startday'];
		$ed = $_POST['endday'];
		$sm = $_POST['startmon'];
		$em = $_POST['endmon'];
		$sy = $_POST['startyea'];
		$ey = $_POST['endyea'];*/
		$startDate = strip_tags($_POST['startdate']);
		$endDate = strip_tags($_POST['enddate']);
		$qPerDay = $_POST['surveyPerDay'];
		if($colmet==0){ // Fixed time research
			$queryh = $_POST['queryh'];
			$querym = $_POST['querym'];
			$querytid = $_POST['querytid'];
			$query_id = $_POST['query_id'];
		}elseif($colmet==1){ // Fixed interval research
			if(isset($_POST['queryh']) && isset($_POST['querym']) && isset($_POST['queryIntH']) && isset($_POST['queryIntM'])){
				if(is_numeric($_POST['queryh']) && is_numeric($_POST['querym']) && is_numeric($_POST['queryIntH']) && is_numeric($_POST['queryIntM'])) {
					$queryh = $_POST['queryh'];
					$querym = $_POST['querym'];
					$queryIntH = $_POST['queryIntH'];
					$queryIntM = $_POST['queryIntM'];
				}
			}
		}
		// Check date data
		/*if(isset($sd) && isset($sm) && isset($sy) && isset($ed) && isset($em) && isset($ey)){
			if(is_numeric($sd) && is_numeric($sm) && is_numeric($sy) && is_numeric($ed) && is_numeric($em) && is_numeric($ey)){
				if(strlen($sy)==3 || strlen($sy)==1 || strlen($ey)==3 || strlen($ey)==1){
					echo "Invalid year field!";
				}else{
					if(strlen($sd)==1){
						$sd = "0".$sd;
					}
					if(strlen($sm)==1){
						$sm = "0".$sm;
					}
					if(strlen($sy)==2){
						$sy = "20".$sy;
					}
					$startDate = "$sy-$sm-$sd";
					if(strlen($ed)==1){
						$ed = "0".$ed;
					}
					if(strlen($em)==1){
						$em = "0".$em;
					}
					if(strlen($ey)==2){
						$ey = "20".$ey;
					}
					$endDate = "$ey-$em-$ed";*/
		if(!empty($name) && !empty($descr) && isset($rid) && is_numeric($colmet)){
			if(validateDatepick($startDate,$endDate,true)){	
					//Set values which have changed
					$oldname = $res->getName();
					if($oldname!=$name){
						$tmp=$res->setName($name);
					}
	
					if($tmp==true || $oldname==$name){
						$olddescr = $res->getDescr();
						if($olddescr!=$descr){ // if the description has changed
							$tmp1=$res->setDescr($descr); // set the new description
						}
						if($tmp1==true || $olddescr==$descr){
							if(is_numeric($colmet)){
								$oldcolmet = $res->getCollMethod(); 
								if($oldcolmet!=$colmet){ // if the collection method has changed
									$tmp2=$res->setCollMethod($colmet); // set the new collection method
								}
							}
							if($tmp2==true || $oldcolmet==$colmet){
								$oldst = $res->getStartTime();
								if($oldst!=$startDate){ // if the start date has changed
									$tmp3=$res->setStartTime($startDate); // change the starting date
									//All subjects have to be checked that other researches don't overlap
									$subjects = $res->users->getSubjects();
									if(mysql_numrows($subjects)>0){ // is there any subject attached to the research
										for($i=0;$i<mysql_numrows($subjects);$i++){ // checking each of the subjact that appeared in the research
											$suid = mysql_result($subjects,$i,'UID'); // get subject id
											$sub = new Subject($suid); // create subject object
											$part = $sub->participatingIn(); // in what research they participate
											if(mysql_numrows($part)>0){ // if subject attached in researches
												for($j=0;$j<mysql_numrows($part);$j++){ 
													$partRid = mysql_result($part,$j,'research_id');
													if($res->checkOverlap($partRid)){ // checking if there is overlap
														$overlaps = true;
														break;
													}else{
														$overlaps = false;
													}
												}
											}else{
												$overlaps=false;
											}
											if($overlaps==true){
												$res->users->rmSubject($suid);	// if there is overlapping then the subject will be removed
																				// Should be the Administrator warned about it?
											}
										}
									}
								}
								if($tmp3==true || $oldst==$startDate){
									$oldend = $res->getEndTime();
									if($oldend!=$endDate){  // if the end date has changed
										$tmp4=$res->setEndTime($endDate); // change the end date
										//All subjects have to be checked that other researches don't overlap
										$subjects = $res->users->getSubjects();
										if(mysql_numrows($subjects)>0){
											for($i=0;$i<mysql_numrows($subjects);$i++){
												$suid = mysql_result($subjects,$i,'UID');
												$sub = new Subject($suid);
												$part = $sub->participatingIn();
												if(mysql_numrows($part)>0){
													for($j=0;$j<mysql_numrows($part);$j++){
														$partRid = mysql_result($part,$j,'research_id');
														if($res->checkOverlap($partRid)){
															$overlaps = true;
															break;
														}else{
															$overlaps = false;
														}
													}
												}else{
													$overlaps=false;
												}
												if($overlaps==true){
													$res->users->rmSubject($suid);	// if there is overlapping then the subject will be removed
																					// Should be the Administrator warned about it?
												}
											}
										}
									}
									if($tmp4==true || $oldend==$endDate){
										$oldqpd = $res->getQueriesPerDay();
										if($oldqpd!=$qPerDay){ // if the number of the queries has changed
											$tmp5=$res->setQueriesPerDay($qPerDay); 
										}
										if($tmp5==true || $oldqpd==$qPerDay){
											$queryTimes = $res->getQueryTimes();				
											if(($colmet==0) && ($oldcolmet==0)){ // if the fixed time collection method has not changed																								
												for($c=1;$c<=$qPerDay;$c++){
													$time = $queryh[$c].":".$querym[$c];
													if($oldqpd==$qPerDay){ // if the number of the queries of the day has not changed
														$qtime = mysql_result($queryTimes,$c-1,"qtime"); // get the time for the actual query
														$qtime_id = mysql_result($queryTimes,$c-1,"qtime_id"); // get the ID of the actual query time
														$q_id = mysql_result($queryTimes,$c-1,"query_id"); // get the ID of the actual query
														if($time!=$qtime && decrypt($query_id[$c])==$q_id){ // if the old query time has changed, then insert the time into the database
															$tmp6=$res->setQueryTimesQueryTime($qtime_id,$time);
														}
													}elseif($qPerDay>$oldqpd){ // if the number of the queries has risen
														if($c<=$oldqpd){
															$qtime = mysql_result($queryTimes,$c-1,"qtime");		// checks if the old queries' times has changed,
															$qtime_id = mysql_result($queryTimes,$c-1,"qtime_id");	// if yes, insert into database 
															$q_id = mysql_result($queryTimes,$c-1,"query_id");
															if($time!=$qtime && decrypt($query_id[$c])==$q_id){
																$tmp6=$res->setQueryTimesQueryTime($qtime_id,$time);
															}
														}else{
															$tmp6=$res->createQueryTimes(0,$time); // insert a new row into the database query time and query
														}
													}else{ // if the number of the queries has decreased
														$qtime = mysql_result($queryTimes,$c-1,"qtime");
														$qtime_id = mysql_result($queryTimes,$c-1,"qtime_id");
														$q_id = mysql_result($queryTimes,$c-1,"query_id");
														if($time!=$qtime && decrypt($query_id[$c])==$q_id){
															$tmp6=$res->setQueryTimesQueryTime($qtime_id,$time);
														}
													}
												}
												if($oldqpd>$qPerDay){ // remove the extra number queries
													for($v=$oldqpd;$v>$qPerDay;$v--){
														$qtime = mysql_result($queryTimes,$v-1,"qtime");
														$qtime_id = mysql_result($queryTimes,$v-1,"qtime_id");
														$q_id = mysql_result($queryTimes,$v-1,"query_id");
														$res->rmQueryTimes($qtime_id);	// remove query from the tbl_query_times 
														$rmdQuery = new Query($q_id);
														$rmdQuery->rmQuery();			// remove query from the tbl_query
													}
												}
											}elseif($colmet==1 && $oldcolmet==1){ // if the fixed interval collection method has not changed
												$firsttime = $queryh.":".$querym.":00";
												$interval = $queryIntH.":".$queryIntM.":00";
												$res->setFixedFirstTime($firsttime);
												$res->setFixedInterval($interval);
												//$res->updateFixedTimes();
												$queryFixedTimes = $res->getFixedTimes();
												for($c=1;$c<=$qPerDay;$c++){ // update the queries in the tbl_fixed_times
													if($oldqpd==$qPerDay){ // if the number of the queries of the day has not changed
														$qfixedtime = mysql_result($queryFixedTimes,$c-1,"fixedtime"); // get the time for the actual query
														$qfixedtime_id = mysql_result($queryFixedTimes,$c-1,"fixedtime_id"); // get the ID of the actual query fixedtime
														$qfixed_id = mysql_result($queryFixedTimes,$c-1,"query_id"); // get the ID of the actual query
														// here could be a method to change the order of the already assigned queries
													}elseif($qPerDay>$oldqpd){ // if the number of the queries has risen
														if($c<=$oldqpd){
															$qfixedtime = mysql_result($queryFixedTimes,$c-1,"fixedtime"); // get the time for the actual query
															$qfixedtime_id = mysql_result($queryFixedTimes,$c-1,"fixedtime_id"); // get the ID of the actual query fixedtime
															$qfixed_id = mysql_result($queryFixedTimes,$c-1,"query_id"); // get the ID of the actual query
															// here could be a method to change the order of the already assigned queries
															
														}else{	// insert new row into the tbl_fixed_times
															$res->insertFixedTime(0, $c); // insert a new row into the tbl_fixed_times
														}
													}else{ // if the number of the queries has decreased
														
													}
												}
												if($oldqpd>$qPerDay){ // remove the extra number queries
													for($v=$oldqpd;$v>$qPerDay;$v--){
														$qfixedtime = mysql_result($queryFixedTimes,$v-1,"fixedtime"); // get the time for the actual query
														$qfixedtime_id = mysql_result($queryFixedTimes,$v-1,"fixedtime_id"); // get the ID of the actual query fixedtime
														$qfixed_id = mysql_result($queryFixedTimes,$v-1,"query_id"); // get the ID of the actual query
														$res->rmFixedTime($qfixedtime_id);	// remove query from the tbl_fixed_times
														$rmdQuery = new Query($qfixed_id);
														$rmdQuery->rmQuery();			// remove query from the tbl_query
													}
												}
											}else{
												if($colmet==0){
													for($c=1;$c<=$qPerDay;$c++){
														$time = $queryh[$c].":".$querym[$c];
														$tmp6=$res->createQueryTimes(0,$time);
													}
												}elseif($colmet==1){
													$firsttime = $queryh.":".$querym;
													$interval = $queryIntH.":".$queryIntM;
													$tmp6=$res->createFixed($firsttime,$interval);
												}
												if(mysql_numrows($queryTimes)>0 && $colmet!=0){
													$res->rmQueryTimes();
												}
												if($colmet!=1){
													$res->rmFixedTime();
												}
											}
											$res->unLock($UID);
										if(!headers_sent()){
						       				header('Location:displayResearch.php?id='.$rid);
							    		}else{
							        		echo '<script type="text/javascript">';
							        		echo 'window.location.href="displayResearch.php?id='.$rid.'";';
							        		echo '</script>';
							        		echo '<noscript>';
							        		echo '<meta http-equiv="refresh" content="0;url=displayResearch.php?id='.$rid.'" />';
							        		echo '</noscript>';
							    		}
										}else{
											echo("<h1>There was a problem updating your researches amount of queries per day!</h1>");
										}
									}else{
										echo("<h1>There was a problem updating your research endtime!</h1>");
									}
								}else{
									echo("<h1>There was a problem updating your research start time!</h1>");
								}
							}else{
								echo("<h1>There was a problem updating your research data collection method!</h1>");
							}
						}else{
							echo("<h1>There was a problem updating your research description!</h1>");
						}		
					}else{
						echo("<h1>There was a problem updating your research name!</h1>");
					}
				}else{
					if(!headers_sent()){
						header('Location:../content/editResearch.php?id='.$rid.'&error=2');
					}else{
						echo '<script type="text/javascript">';
						echo 'window.location.href="../content/editResearch.php?id='.$rid.'&error=2";';
						echo '</script>';
						echo '<noscript>';
						echo '<meta http-equiv="refresh" content="0;url=../content/editResearch.php?id='.$rid.'&error=2" />';
						echo '</noscript>';
			}
				}						
		}else{
			if(!headers_sent()){
				header('Location:../content/editResearch.php?id='.$rid.'&error=1');
			}else{
				echo '<script type="text/javascript">';
				echo 'window.location.href="../content/editResearch.php?id='.$rid.'&error=1";';
				echo '</script>';
				echo '<noscript>';
				echo '<meta http-equiv="refresh" content="0;url=../content/editResearch.php?id='.$rid.'&error=1" />';
				echo '</noscript>';
			}
		}
}
?>