<?php
if(!headers_sent()){	// if headers have not been sent
	header('Location:./content/index.php');	// then calls the ./content/index.php file
}else{					// if headers have been sent
    echo '<script type="text/javascript">';	// uses java script to load ./content/index.php
	echo 'window.location.href="./content/index.php";';
	echo '</script>';
	echo '<noscript>';	// if no JavaScript is supported
	echo '<meta http-equiv="refresh" content="0;url=./content/index.php" />';
	echo '</noscript>';
}
?>
