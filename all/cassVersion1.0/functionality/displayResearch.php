 <?php
if($r->users->isLocalAdmin($UID) || $r->users->isLocalResearcher($UID)){
		if($id!=null){
			$research = new Research($id);	// creates a research object
			$name= $research->getName();	// gets the name of it
			$descr = $research->getDescr();	// gets the description
			$method = $research->collMethod2String($research->getCollMethod());	// gets the data collection method
			$start = $research->getStartTime();	// gets the start date
			$end = $research->getEndTime();		// gets the end date
			$queries = $research->getQueriesPerDay();	// gets the number of the queries per day
			$status = $research->getStatus();	// gets the status of the research
			$created = $research->getCreated();	// gets the date and time of the creation

			//Actual information in center
			// Navigation path
			echo "<div id=\"navpath\"><a href=\"../content/index.php\"> Home </a> >> <a href=\"#\">$name</a></div>";
			echo "<div id=\"descriptiondiv\">";
			// name of the research
			echo("<div class=\"descrheader\"><div class=\"headertext\"><h1>$name</h1><hr /></div></div>");
			echo "<i class=\"backlink\">Created: $created</i>";
			// description
			echo("<div class=\"description\"><b>Description: </b> <div class=\"descrtext\">$descr</div></div>");
			echo "<div class=\"timeinterval\"><b>Time interval: </b>";
			echo "<div class=\"timeinttext\">";
			echo "Starts: $start<br />";
			echo "Ends: $end<br />";
			echo "</div></div>";
			echo("<div class=\"colmet\"><b>Data collection method: </b> $method <div class=\"colmettext\">");
			if($research->getCollMethod()!=2){
				echo "$queries queries are sent per day<br />";
			}
			echo "</div>";
			echo "</div>";
			// if the research is fixed time or fixed interval research
			if($research->getCollMethod()==0 || $research->getCollMethod()==1){
				echo "<div class=\"qrtimes\">";
				// if the research is fixed time research
				if($research->getCollMethod()==0){
					echo "<b>Query times:</b><div class=\"qrtimestext\">";
					$qtimes = $research->getQueryTimes();	// getting the research times into a mysql table
					for($i=0;$i<mysql_numrows($qtimes);$i++){	// getting all the info from the mysql table
						$qtime = mysql_result($qtimes,$i,"qtime");
						$queryid = mysql_result($qtimes,$i,"query_id");
						$queryid_array[$i] = $queryid; // creating an array to store the order of the queries
						if($queryid!=0){	// if there is a query already assigned to the time
							$query = new Query($queryid);
							$qname = $query->getName();
							echo "$qtime: assigned for query: $qname<br />";
						}else{	// if there is no query assigned to the time
							echo "$qtime<br />";
						}
					}
					echo "</div>";
				// if the research is fixed interval research
				}elseif($research->getCollMethod()==1){
					$firsttime = $r->getFixedFirsttime();	// getting the time of the first query
					$interval = $r->getFixedInterval();		// getting the interval between queries
					
					$qFixedTimes = $research->getFixedTimes(); // get the fixed times for an array
					for($i=0;$i<mysql_numrows($qFixedTimes);$i++){
						$queryid = mysql_result($qFixedTimes,$i,"query_id");
						$queryid_array[$i] = $queryid; // creating an array to store the order of the queries
					}
					
					echo "<b>Fixed time:</b><div class=\"qrtimestext\">";
					echo "First query set at: ".$firsttime;
					echo "<br />Interval between queries: ".$interval;
					
					echo "</div>";
				}
				echo "</div>";
				
			}
			// if the research is event contingent
			elseif($research->getCollMethod()==2){
				$query = $research->listChildren();
				if(mysql_numrows($query) == 0){ // This check whether query has been assigned,
					$queryid_array[0] = 0;		// whether there is any query rows in the research 
				}else{
				$queryid = mysql_result($query, 0, "query_id");
				$queryid_array[0] = $queryid;}
			}
			
			echo "<br />";
		
			 // calls in functions for generating the "Queries in the research" list
			require_once("../functionality/listQueries.php");
			
			// calls in functions for generating the "Users in the research" list
			require_once("../functionality/listUsers.php");
				
			// Sorted query list:
			echo '<div id="queriesdiv">';
				echo '<div class="descrheader"><div class="headertext"><h1>Queries in the research</h1><hr /></div></div>';
				echo '<div class="queriescontent">';
				if(($status=="On Progress")&&($r->isLocked()!="freezed")){
					echo '<h2>WARNING!</h2>';
					echo '<p>Ongoing research. Changing or removing queries might ruin the collected data.</p>';
				}
					echo '<table cellspacing="10" cellpadding="1">';
					 echo '<tr><th></th><th>Name</th><th>Actions</th><td></td></tr>';
					 	$mofo2 = $research->listChildren();	// gives back an SQl array with all the queries in the research
					 	list ($queries_sorting) = getQueryList($mofo2,$r->users->isLocalAdmin($UID),$r->isLocked(),$id);
					 	/*
					 	 * Running a for loop the on the $query_id array.
					 	 * This stores the right order of the queries.
					 	 * If the $queryid_array[$i] is not NULL, meaning that the query
					 	 * has been set up, then it runs another for loop. The latter one
					 	 * tries to match the number in the $query_id array with the number in
					 	 * $queries_sorting[$c]['query_id]. If they match, a new row is printed
					 	 * in the table.
					 	 */
					 	for ($i=0; $i < count($queryid_array); $i++){
					 		if ($queryid_array[$i] != 0){
					 			for ($c=0; $c <= count($queries_sorting); $c++){
					 				if ($queryid_array[$i] == $queries_sorting[$c]['query_id']){
					 					echo '<tr><td>#'.($i+1).'</td><td><a href="./displayQuery.php?id=' . encrypt($queries_sorting[$c]['query_id']) .
					 					'">' . $queries_sorting[$c]['name'] . '</a></td>';
					 					if ($queries_sorting[$c]['edit']!=0){	// If the edit column has a value, it means the query can be edited
					 															// Rights are checked in the getQueryList function
					 						echo '<td><a href="./manipQuery.php?id=' . encrypt($queries_sorting[$c]['edit']) .
					 						'">Edit question</a></td>';
					 						echo '<td><a href="#" onClick="wantToRemoveQuery(\'' . encrypt($queries_sorting[$c]['edit']) . '\')">
					 						Remove query</a></td>'; 
											/*
											 * Show link to visualization page
											 * if visualize column is true.
											 * @notTrue show link
											 * @false do nothing
											 * @link to content/visualize.php carrying the queryID
											 * */
											 //$query = new AnswerSQLQueryer();
											 //$visual = $query->checkVisualize($queries_sorting[$c]['query_id'], $id);//mysql_query("SELECT `visualize` FROM  `tbl_query` WHERE query_id= $queries_sorting[$c]['query_id'] AND research_id = $id"); 
											// if ($visual!=NULL) {
											//	echo "<td><a href=\"visualize.php?qID=".encrypt($queries_sorting[$c]['query_id'])."\" target=\"_blank\">Visualize</a></td>";
											// }
					 					}
					 				}
					 				echo '</tr>';
					 			}
					 		}elseif ($queryid_array[$i] == 0){
					 			echo '<tr><td>#'.($i+1).'</td><td>This query has<br/>not yet been assigned.</td></tr>';
					 		}
					 	}
					 	if ($queries_sorting == 0){
					 		echo '<tr><td colspan="4">There are no queries in this research.</td></tr>';
					 	}
					echo '</table>';
				echo '</div>';
			echo '</div>';
			
			//users
			echo "<div id=\"usersdiv\">";
				echo "<div class=\"descrheader\"><div class=\"headertext\"><h1>Users in the research</h1><hr /></div></div>";
				echo "<div class=\"usercontent\">";
					echo "<table border=\"0\" style=\"vertical-align:top\" cellspacing=\"10\">";
						echo "<tr><td valign=\"top\">";
						//geting the admins from the research
						$result = $research->users->getAdmins();
						// generates the user list from the MySQL result table
						genUserList("Administrators", $result);
						echo"</td><td valign=\"top\">";
						//geting the researchers from the research
						$result = $research->users->getResearchers();
						// generates the user list from the MySQL result table
						genUserList("Researchers", $result);
						echo"</td><td valign=\"top\">";
						//geting the subjects from the research
						$result = $research->users->getSubjects();
						// generates the user list from the MySQL result table
						genUserList("Respondents", $result,$r->users->isLocalAdmin($UID),$id);
						echo '</td></tr>';
						echo '<tr><td></td><td></td><td align="center">';
						/*
						 * No functionality for setting the tokens, is nonesense. 
						 */
						//echo '<a style="text-align: right;" href="./addBtIdAll.php?id='.encrypt($id).'"><span>Set Bluetooth IDs</span></a>';
						//echo '<a style="text-align: right;" href="./addBtIdAll.php?id='.encrypt($id).'"><span>Set tokens</span></a>';
						echo "</td></tr></table>";
					echo "</div></div>";
					echo '<a class="button" href="./index.php"><span><< Researches</span></a>';
				echo "</div>";
			echo "</div>";	
			//Div element in the right
			echo "<div id=\"right\">";
			echo "<div id=\"rightContent\">";
			$enId= encrypt($id);
			if( $r->users->isLocalAdmin($UID)){	// checks if the user is local admin
				echo "<div class=\"actiontitle\"><div class=\"actiontext\">Actions</div></div>";
				// removing the research
				echo "<div class=\"menuitem\"><a href=\"#\" onClick=\"wantToRemoveResearch('$enId')\"> >> Remove this research</a></div>";
				/*
				 * If the research is not locked and the research has not ended, the it prints a link to edit
				 * the research. isLocked function is called from the research class(Research.php)-> from ResearchSQLQueryer.php.
				 * This method check also the time contstraint.
				 */ 
				if($r->isLocked()==false){
					echo "<div class=\"menuitem\"><a href=\"./editResearch.php?id=$enId\"> >> Edit this research</a></div>";
				}
			}
			echo "<div class=\"actiontitle\"><div class=\"actiontext\">Information</div></div>";
			/*
			 * for getting the information and data about the research.
			 * Clicking on the link will call the getfiles function from AjaxAdd/function.js
			 * The links here will call functions from functionality/getAnswers.php
			 */ 
			echo "<div class=\"menuitem\"><a href=\"#\" onclick=\"getfiles('getResearch','$enId');\"> >> Get research info</a></div>";
				/*
				 * Download the list of users:
				 * username, password and token
				 * if(subjects on research)
				 */
				//if($research->users->getSubjects()==true){
					echo "<div class=\"menuitem\"><a href=\"#\" onclick=\"getfiles('getUserInfo', '$enId')\"> >> Get user info</a></div> ";
				//}
			if($status!="Starts"){
				echo "<div class=\"menuitem\"><a href=\"#\" onclick=\"getfiles('getResearchAnswers','$enId');\"> >> Get research data</a></div>";
				echo "<div class=\"menuitem\"><a href=\"#\" onclick=\"getfiles('getResearchAnswers2','$enId');\"> >> Get research data#2</a></div>";
				echo "<div class=\"menuitem\"><a href=\"#\" onclick=\"getfiles('getResearchAnswers3','$enId');\"> >> Get research data#3</a></div>";
				echo "<div class=\"menuitem\"><a href=\"#\" onclick=\"getfiles('getResearchZip','$enId');\"> >> Get media files</a></div>";
			}
							
			if($status=="On Progress"){ // if the research has started
				if($r->isLocked()=="freezed"){ // if the research is locked then it shows here
					echo "<div class=\"menuitem\"><div id=\"freezed\"><u>RESEARCH IS LOCKED!</u></div></div>";					
				}else{ // if the research is not locked
					if($r->users->isLocalAdmin($UID)){	//  and the user is research administrator puts a link to freeze the research
						$enUID = encrypt($UID);	
						$enId = encrypt($id);
						echo "<div id=\"freeze\" onClick=\"freezeResearch('$enId','$enUID')\"><span class=\"lockTxt\">LOCK RESEARCH</span></div>";
					}
				}
			}
			echo "</div>";	
				
	}else{
		echo("<h1>Error: No research specified!</h1>");
	}
}else{
	echo "Access denied,you are not allowed to browse this data.";
}

?>
