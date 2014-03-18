<?php
require("../common/includes.php");
if (isset($_GET['xmlSend'])){
	$data = $_GET['xmlSend'];
echo $data;
}else
$data = $HTTP_RAW_POST_DATA;
$answer = new XMLAggregator();
$answer->readXml($data);
?>