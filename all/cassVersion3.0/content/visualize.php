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
						$cha_px = (527 - 74 - ($cha-1)*65.4167)*1.5;
						
						//For the averageDot
						$sumCha += $row->answer;
						
			    	} else if($row->number==4){
			    		 /* Getting competence result
						  * @return integer
						  */
						$com = $row->answer;
						// width of the left border - accurating point + times(1 in graph would be 0, thus -1) * average width of one square
						$com_px = (133.5 - 8 + ($com-1)*65.4167)*1.5;
						
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
				$aveCha_px = (527 - 84 - ($aveCha-1)*65.4167)*1.5;
				// -18, here it changes due to accurateness of the point.
				$aveCom_px = (133.5 - 18 + ($aveCom-1)*65.4167)*1.5;
				$toPrint .= "<p class=\"average\">Average: $aveCom, $aveCha</p>";
				$readyDots .= "<img id=\"aveDot\"class=\"aveDot\" title=\"Average\" src=\"../CassQbrowser/aveDot.png\" style=\"left:".$aveCom_px."px;top:".$aveCha_px."px; position:absolute;\">";
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
				width:1.5em;
				height:1.5em;
				background-color:#4B4B4B;
				-moz-border-radius: 50%;
				-webkit-border-radius: 50%;
				-khtml-border-radius: 50%;
				border-radius: 50%;
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
			#container {margin:0 auto; width:659px; max-width:659px;min-width:659px;background-image:url("../CassQbrowser/visualize_bg.jpg");background-repeat:no-repeat;}
		
		</style>

	</head>
	
	<body>
<img id="bg" src="../CassQbrowser/visualize_bg.jpg" title="Query's name" width="988.5px" height="801px"/>
	
	
	<?php
	echo "<div id=\"rightside\">";
	echo "<h1>Query: $nameQuery </h1>";
	echo $toPrint;
	echo "</div>";
	echo $readyDots;
	?>
<div>
<button id="hide">Hide</button>
<button id="show">Show</button>
</div>

<script src="http://code.jquery.com/jquery-latest.js"></script>

		<script type="text/javascript">
$(document).ready(function(){
	$("#rightside").hide();
  $("#hide").hide();
  $("#hide").click(function(){
	  var bg_larger_width =  ($("#bg").width())*1.5;
	  var bg_larger_height =  ($("#bg").height())*1.5;
var spans = $("span");
var span_num = $("span").size();
var i=0;
while(i< span_num){
var dot_left  = spans.eq(i).css("left");
var dot_top  = spans.eq(i).css("top");


spans.eq(i).css("left",parseFloat(dot_left)*1.5);
spans.eq(i).css("top",parseFloat(dot_top)*1.5);
spans.eq(i).css("width","1.5em");
spans.eq(i).css("height","1.5em");
i ++;
}
$("span").attr({ width:"1.5em",height:"1.5em"});
var ave_left = $("#aveDot").css("left");
var ave_top = $("#aveDot").css("top");
$("#aveDot").css("left",parseFloat(ave_left)*1.5);
$("#aveDot").css("top",parseFloat(ave_top)*1.5);
	    $("#rightside").hide("fast");
	    $("#bg").width(bg_larger_width);
	    $("#bg").height(bg_larger_height);
	    $("#bg").show("slow");
    $("#hide").hide();
    $("#show").show();
  });
  $("#show").click(function(){
	  var bg_smaller_width =  ($("#bg").width())/1.5;
	  var bg_smaller_height =  ($("#bg").height())/1.5;
var spans = $("span");
var span_num = $("span").size();
var i=0;
while(i< span_num){
var dot_left  = spans.eq(i).css("left");
var dot_top  = spans.eq(i).css("top");


spans.eq(i).css("left",parseFloat(dot_left)/1.5 + "px");
spans.eq(i).css("top",parseFloat(dot_top)/1.5 + "px");
spans.eq(i).css("width","1em");
spans.eq(i).css("height","1em");
i ++;
}

var ave_left = $("#aveDot").css("left");
var ave_top = $("#aveDot").css("top");
$("#aveDot").css("left",parseFloat(ave_left)/1.5 + "px");
$("#aveDot").css("top",parseFloat(ave_top)/1.5 + "px");

    $("#rightside").show("slow");
    $("#bg").width(bg_smaller_width);
    $("#bg").height(bg_smaller_height);
    $("#bg").show("slow");
    $("#hide").show();
    $("#show").hide();
    
  });
});



</script>
	</body>
</html>