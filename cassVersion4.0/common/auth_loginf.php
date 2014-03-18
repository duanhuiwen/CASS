<?php
/* Takes three arguments: last attempted username, the authorization
 * status, and the Auth object. */ 
function loginFunction($username = null, $status = null, &$auth = null){
    require_once("../UI/layout/top.php"); //The top part of the layout
    echo "<div id=\"loginf\">"; // These lines print the form for the logging in
    	echo "<form method=\"post\" action=\"index.php\">";
    		echo("<table>");
 				echo("<tr>");
    				echo("<td colspan=\"2\"><h1>Login</h1></td></tr>");
    			echo "<tr><td>Username:</td><td><input type=\"text\" name=\"username\" size=\"20\" value=\"$username\" /></td></tr>";
    			echo "<tr><td>Password:</td><td><input type=\"password\" name=\"password\" size=\"20\" /></td></tr>";
    			echo "<tr><td>&nbsp;</td><td><input type=\"submit\" value=\"Login\" /></td></tr>";
    		echo("</table>");
    	echo "</form>";
    echo "</div>";
}
?>