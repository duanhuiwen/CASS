<?php
	error_reporting(0);		/*connecting to DB by PEAR::MDB2*/
							require_once 'MDB2.php';	
							$mdb2 = MDB2::connect('mysql://cassuser:Puimur1@localhost/cass');//$mdb2 = MDB2::connect('mysql://root@localhost/cass');	//'db'://'username':'pass'@'servername'/'dbname'
							if (PEAR::isError($mdb2)) {
									die($mdb2->getMessage());
							}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fi" lang="fi">
	<head>
	<title>CassQ Browser Client</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="client.css"  />
	<script type="text/javascript" src="funs.js"></script>
	<link rel="shortcut icon" href="../UI/layout/favicon.ico" type="image/x-icon" />
	<!--SLIDER-->
	<script type="text/javascript">window.dhx_globalImgPath="codebase/imgs/";</script>
    <script src="codebase/dhtmlxcommon.js"></script>
    <script src="codebase/dhtmlxslider.js"></script>
	<script src="codebase/ext/dhtmlxslider_start.js"></script>
	<link rel="stylesheet" type="text/css" href="codebase/dhtmlxslider.css">
	<!--END OF DECLARATION-->
	</head>
	<body>
		<div id="wrapper">
			<div id="banner">
				<script type="text/javascript">
					if (screen.width > 350){
						document.write('<img src="../UI/layout/cass.gif" width="761" height="236" alt="CASS-Q Administrator" />');
					} else {//for HANDHELD SCREEN
						document.write('<img src="cass_mob.gif" width="240" height="75" alt="CASS-Q Administrator" />');
					}
				</script>
			</div>
			<div id="local"></div>
			<script type="text/javascript">
				if (!localStorage.uid)
					document.getElementById("local").innerHTML="<h1>Please enter Your BT_ID:</h1>";
				else
					document.getElementById("local").innerHTML='<h1>This is the current BT_ID for this browser:</h1><h1 style="color:red">'+localStorage.uid+'</h1>';
			</script>
			<!--LOCAL STORAGE ENTER-->						
			<form style="display:none" id="enter" action="cassQbrowser.php" onsubmit="saveLocal(this)" method="post">
				<input type="text" size="12" name="bt_id" />
				<br />
				<br />
				<input type="submit" value="Save">
			</form>
			<!--CONTENT-->
			<div id="xml_area"></div>
			<div id="q"></div>
			<!--LOCAL STORAGE RESET-->
			<form id="reset" action="cassQbrowser.php" onsubmit="resetLocal()">
				<input type="submit" value="Reset Local BT_ID"/>
			</form>
			
			<script type="text/javascript">
				if (!localStorage.uid){
					document.getElementById("enter").style.display="inline";
					document.getElementById("reset").style.display="none";
				}else{					
				//	xmlDoc = loadXMLDoc();
					ShowSurvey();
				}
			</script>
			<!--FOOTER-->
			<div id="footer">CASS-Q Client -Metropolia University of Applied Sciences, Knowledge Practices Laboratory</div>
		</div>
	</body>