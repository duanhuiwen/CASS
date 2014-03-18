
<?php 

$eqid = encrypt($qid);
if($query->isVisualize() == 1){
	//echo $query->isVisualize();
	echo "<form action=\"../functionality/addVisualization.php\" method=\"GET\">
	<input type=\"checkbox\" name=\"visualize\" value=\"true\" checked/>Visualize
	<input type=\"hidden\" name = \"qid\" value = \"$eqid\" />
	<div title= \"what is this visualization all about?\" id=\"help\" onclick=\"openhelp('visualize');\">?</div>
	<br><input type=\"submit\" name=\"submit\" value=\"Finished\"/>
	</form>";
}else{
	//echo $query->isVisualize();
	echo "<form action=\"../functionality/addVisualization.php\" method=\"GET\">
<input type=\"checkbox\" name=\"visualize\" value=\"true\" />Visualize
<input type=\"hidden\" name = \"qid\" value = \"$eqid\"  />
<div title= \"what is this visualization all about?\" id=\"help\" onclick=\"openhelp('visualize');\">?</div>
<br>
<input type=\"submit\"/  name=\"submit\" value = \"Finished\" >
</form>";

}

//echo $eqid;

?>
