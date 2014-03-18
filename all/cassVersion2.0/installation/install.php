<?php 
/*
 * This file is needed only for the installation of the system and ha to be run only once.
 */
if(!isset($_POST['submit'])){ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fi" lang="fi">
	<head>
		<title>Cass-Q Admin Installation </title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="../UI/layout/cass.css" type="text/css" />
		<link rel="stylesheet" type="text/css" href="../UI/layout/dropdown.css" />
		<link rel="stylesheet" type="text/css" href="../UI/layout/smoothness/jquery-ui-1.7.1.custom.css" />		
		<script type="text/javascript" src="../AjaxAdd/functions.js"></script>
		<script src="../AjaxAdd/jquery-1.3.2.min.js" type="text/javascript"></script>
		<script src="../AjaxAdd/jquery-ui-1.7.1.custom.min.js" type="text/javascript"></script>
		<link rel="shortcut icon" href="../UI/layout/favicon.ico" type="image/x-icon" />		
	</head>
	<body>
		<div id="wrapper">
			<div id="banner">
				<img src="../UI/layout/cass.gif" width="761" height="236" alt="CASS-Q Administrator" />	
			</div>
			<div id="content" style="margin-left: 20px">
				<br />
				<br />
				<h1>CASS-Q installation</h1>
				<h2>Requires that <a href="http://www.php.net/downloads.php">PHP 5.2+</a> with <a href="http://pear.php.net/package/PEAR/download">PHP PEAR</a> and <a href="http://www.apachefriends.org/">MySQL</a> is installed and running</h2>
				
				<form action="install.php" method="post">
				Database Server: <input type="text" name="dbserver" class="dbinput" /><br /><br />
				Database Name: <input type="text" name="dbname" class="dbinput" /><br /><br />
				Database User Name: <input type="text" name="dbuser" class="dbinput" /><br /><br />
				Database Password: <input type="password" name="dbpwd" class="dbinput" /><br /><br />
				Media Files: <br /><ul><li class="dbmedia">To Database: <input type="radio" name="mediafiles" value="db" class="dbradio" checked="checked" /></li><br />
				<li class="dbmedia">To Server: <input type="radio" name="mediafiles" value="serv" class="dbradio" /></li></ul><br /><br />
				
				Super Admin User Name: <input type="text" name="supername" class="dbinput" /><br /><br />
				Super Admin Password: <input type="password" name="superpwd" class="dbinput" /><br />
				<br />
				<br />
				<input type="submit" name="submit" value="Install" /><br />
				</form>
			</div>
		
			<div id="footer">
				CASS-Q Administrator - EVTEK University of Applied Sciences, Knowledge Practices Laboratory
			</div>
		</div>
	</body>
</html>

<?php
}else{
	$file = fopen("../settings/dbset.php","r");
	$content = fread($file,filesize("../settings/dbset.php"));
	fclose($file);
	
	$dbsettings = str_replace("-server-",$_POST['dbserver'],$content);
	$dbsettings = str_replace("-db-",$_POST['dbname'],$dbsettings);
	$dbsettings = str_replace("-usr-",$_POST['dbuser'],$dbsettings);
	$dbsettings = str_replace("-pwd-",$_POST['dbpwd'],$dbsettings);
	if($_POST['mediafiles']=="db"){	
		$dbsettings = str_replace("-media-",true,$dbsettings);
	}else{
		$dbsettings = str_replace("-media-",false,$dbsettings);
	}
	$file = fopen("../settings/dbsettings.php","w"); // Change back to dbsettings.php, also in CreateDB.php
	fwrite($file,$dbsettings);
	fclose($file);
	
	$con = mysql_connect($_POST['dbserver'],$_POST['dbuser'],$_POST['dbpwd']);
	if (!$con){
 		 die('Could not connect: ' . mysql_error());
    }

	if (mysql_query("CREATE DATABASE ".$_POST['dbname']."",$con)){
 		echo "Database created<br />";
 		include "../settings/dbsettings.php"; // Change back to dbsettings.php, also in CreateDB.php
 		include "../settings/CreateDb.php";
 		
 		if(new CreateDb($_POST['supername'],md5($_POST['superpwd']))){
 			echo "<h1>Database created succesfully!</h1>";
 			
 			
 			// Updating the CassQ.jad file for the actual server:
 			// Test first if it works. If not, delete!
 			echo "<h2>Reading the mobile client installation file.</h2>";
 			if(!$file = fopen("CassQ_install.jad" ,'r')){
 				return false;
 				echo"<h3>Could not read the mobile client installation file!</h3>";
 			}else{
 				echo "<h3>OK.</h3>";
 			}
			$content = fread($file,filesize("CassQ_install.jad"));
			fclose($file);
			
			echo "<h2>Changing the paths to the actual ones.</h2>";
			$server = $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
			$server = str_replace("/installation/install.php","",$server);
			
			$jadsettings = str_replace("-server-",$server,$content);
			
			echo "<h2>Creating a new JAD file for the mobile installation!</h2>";
			if(!$file = fopen("CassQ1.jad","w")){ // for testing use: CassQ1.jad
				return false;
			}
			if(fwrite($file,$jadsettings===FALSE)){
				return false;
				echo"<h3>Creating JAD file was not successful!</h3>";
			}else{
				echo"<h3>OK!</h3>";
			}
			fclose($file);
			
			
 			
 			echo "<a href=\"../content/index.php\">Login to Cass-Q Admin tool</a>";
 		}else{
 			echo "Database creation failed!";
 		}
		
    }else{
 		 echo "Error creating database: " . mysql_error();
	}

	mysql_close($con);
}
?>