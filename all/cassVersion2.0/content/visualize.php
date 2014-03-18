<?php
			require_once("../common/auth_loginf.php"); //Login form generation function include
			require_once("../common/auth_start.php");	// Authentication include
			require_once("../common/includes.php"); //Class includes
			$qID= decrypt($_GET['qID']);
			
			$query = new AnswerSQLQueryer();
			$raw_data = $query->visualizeQuery($qID);
			
			$numUsers = 0;
			/*
			 * Get all the users and their answers.
			 * @var resource ID from the query
			 */
				 /*
				  * Print data in table
				  */
				$toPrint;
				$toPrint = "<table id=\"visualizeTable\">
							<thead><tr><th>Username</th><th>Competence</th><th>Challenge</th><th>Time</th></tr></thead>
							<tbody>";
			while ($row = mysql_fetch_object($raw_data)) {
				$numUsers++;
				$nameQuery = $row->name;
			    if($row->question_type == 9){
			    	if($row->number == 3){
			    		 /* Getting challenge result
						  * @return integer
						  */
						$cha = $row->answer;
						//calculating pixels for the position.
						//height of image - border from the top - times(1 in graph would be 0, thus -1) * square width
						$cha_px = 527 - 74 - ($cha-1)*65.4167;
						
						//For the averageDot
						$sumCha += $row->answer;
						
			    	} else if($row->number==4){
			    		 /* Getting competence result
						  * @return integer
						  */
						$com = $row->answer;
						// width of the left border - accurating point + times(1 in graph would be 0, thus -1) * average width of one square
						$com_px = 133.5 - 8 + ($com-1)*65.4167;
						
						$toPrint .= "<tr><td>$row->username</td><td>$com</td><td>$cha</td><td> $row->time</td></tr>";
						
						$readyDots .= "
						<span class=\"grayDot\" title=\"$row->username at $row->time\" style=\"left:".$com_px."px;top:".$cha_px."px; position:absolute;\">&nbsp;</span>";
						
						//For the averageDot
						$sumCom += $row->answer;
			    	}
					
				}				
			}
			$toPrint .= "</tbody></table>";
			//The real number of users is half of the answers, since one user answers two questions only.
			$numUsersReal = $numUsers/2;
			/*
			 * Average of the Challenge and the Competence of the users
			 * @return rounded integer
			 */
			if($numUsersReal !=0 && $numUsersReal!=1){
				$aveCha = round($sumCha/$numUsersReal, 1);
				$aveCom = round($sumCom/$numUsersReal, 1);
				// -84, here it changes due to accurateness of the point.
				$aveCha_px = round(527 - 84 - ($aveCha-1)*65.4167, 0);
				// -18, here it changes due to accurateness of the point.
				$aveCom_px = round(133.5 - 18 + ($aveCom-1)*65.4167, 0);
				$toPrint .= "<p class=\"average\">Average: $aveCom, $aveCha</p>";
				$readyDots .= "<img class=\"aveDot\" title=\"Average\" src=\"../CassQbrowser/aveDot.png\" style=\"left:".$aveCom_px."px;top:".$aveCha_px."px; position:absolute;\">";
			} else {
				$toPrint .= "<p> Only one or none user, no average</p>";
			}
		?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>Visualization of <?php echo $nameQuery ?></title>
		<meta content="text/html; charset=ISO-8859-4" />
		<style type="text/css">
			@charset "utf-8";
			/* 
				Resets default browser settings
				reset.css
			*/
			html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,font,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,caption { 
				margin:0; padding:0; border:0; outline:0; font-weight:inherit; font-style:inherit; font-size:100%; font-family:inherit; vertical-align:baseline; }
			:focus { outline:0; }
			a:active { outline:none; }
			
			caption { text-align:left; font-weight:normal; }
			blockquote:before,blockquote:after,q:before,q:after { content:""; }
			blockquote,q { quotes:"" ""; }
			
		 	/*CSS for visualize.php*/
		 	body {
		 		font-family:Arial, Helvetica, sans-serif;
				line-height:1.2; 
				
				text-shadow:1px 1px 2px rgba(48,80,82,0.8);
				background:#6FCBA6;
			}
			h1 {
				font-weight: 700;
				font-size:1.5em;
				padding-top:15px;
				margin-left: 10px;
				color:#064F68;
			}
			#graph {
				top:0;
				left:0;
				background-image:url('../CassQbrowser/visualize_bg.jpg');
				width: 659px; 
				height:534px;
				float:left;
			}
			#graph img {
				margin-right:0;
			}
			#graph img{ 
				top:0; 
				left:0;
				width:659 px;
				height:534 px;
				max-width:659px; 
				max-height:534px;
				margin-right:20px;
			}
			.error { 
				color:#C00;
			}
			
			/* Individual dots*/
			.grayDot {
				position:absolute;
				width:1em;
				height:1em;
				background-color:#4B4B4B;
				-moz-border-radius: 8px;
				-webkit-border-radius: 8px;
				-khtml-border-radius: 8px;
				border-radius: 8px;
				border-color:#4B4B4B;
			}
			/* Container of context text*/
			#rightside {
				position: absolute;
				top:0;
				right:0;
			}
			
			/* Average */
			.aveDot {
				position:absolute;				
			}
			.average {
				font-weight:800;
				font-size:1.2em;
				color:	#FF7519;
			}
			
			/*Table*/
			#visualizeTable{
				font-family:"Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
				font-size:12px;
				line-height:2;
				width:415px;
				border-collapse:collapse;
				text-align:left;
				margin:20px;
			}
			#visualizeTable th{
				font-size:20px;
				font-weight:900;
				color:#F5F2E7;
				border-bottom:2px solid #6678b1;
				padding:10px 8px;}
			#visualizeTable td{
				color:#FFFFFF;
				font-size:16px;
				padding:9px 8px 0;
			}
			#visualizeTable tbody tr:hover td{
				color:#064F68;
			}			
		</style>
	</head>
	
	<body>
	<img src="../CassQbrowser/visualize_bg.jpg" title="Query's name" width="659" height="534"/>
	
	<?php
	echo "<div id=\"rightside\">";
	echo "<h1>Query: $nameQuery </h1>";
	echo $toPrint;
	echo "</div>";
	echo $readyDots;
	?>
	</body>
</html>