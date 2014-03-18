<?php
require_once("MDB2.php");	// This is a PEAR extension
$mdb_type ='mysql';
$mdb_passwd ='cassuser'; 
$mdb_usn ='root';
$mdb_server='10.249.74.181';#localhost also works
$mdb_db ='cass';
$enc=true;
$enc_key="glxBllt"; //Only needed if $enc == true;
$files2db=false; //originally set to true
//The next 4 are needed if the previous one is set to false
 $mediaFolderPath="../MediaFiles/";
 $pictureFolder="picture/";
 $videoFolder="video/";
 $soundFolder="sound/";
//The rest is always needed, hands off!
	$mdb2 =& MDB2::connect("$mdb_type://$mdb_usn:$mdb_passwd@$mdb_server/$mdb_db");
	
if (PEAR::isError($mdb2)) {
    die($mdb2->getMessage());
}
?>
