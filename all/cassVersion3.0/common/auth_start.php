<?php
require_once "Auth.php"; // PEAR extension

$options = array( // mysql://cassuser:Puimur1@localhost/cass    changed by carolir
		'dsn' => "mysql://root@0.0.0.0/cass", // string that will be used to connect to the database via PEAR::DB
  		'table'=>"tbl_auth",
  		'db_fields'=>array('uid', 'su_admin', 'research_owner')
 			 );

 /*
  * create the authentication object.
  * First param.: defines the name of the storage container (MDB2)
  * Second param.: connection parameter for container driver
  * Third paraml.: name of our function that we defined at the beginning of the script.
  *  It prints the login form.  
  */
$a = new Auth("MDB2", $options, "loginFunction");
$timeout = 3;
$a->start(); /*
              * Start the authentication process. To do this Auth checks for an existing
              * session and checks its validity. If there is no existing session or the
              * previous session has been ended for some reason then the login form is
              * generated either via the specified callback or using the default form
              * implemented in Auth_Frontend_HTML.
              */
?>
