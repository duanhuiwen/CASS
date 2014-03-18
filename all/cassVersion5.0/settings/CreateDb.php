<?php
/*
 * This script creates all the tables in the predefined database. It is called form the
 * install.php. After creating the tables, it inserts the super admin user
 * into the tbl_auth table.
 */

class CreateDb{
	var $mdb2;

	function __construct($name,$pwd){
		include ("../settings/dbsettings.php"); // for test use: settings/dbsettings1.php
		$this->mdb2 = $mdb2;
		
		if(!$this->query($this->createAnswer())){
			return $this->fail();
		}
		
		if(!$this->query($this->createAuth())){
			return $this->fail();
		}
		
		if(!$this->query($this->createFixed())){
			return $this->fail();
		}
		
		if(!$this->query($this->createFixedTimes())){
			return $this->fail();
		}
		
		if(!$this->query($this->createLog())){
			return $this->fail();
		}
		
		if(!$this->query($this->createMedia())){
			return $this->fail();
		}
		
		if(!$this->query($this->createNum())){
			return $this->fail();
		}
		
		if(!$this->query($this->createOption())){
			return $this->fail();
		}
		
		if(!$this->query($this->createPrivar())){
			return $this->fail();
		}
		
		if(!$this->query($this->createQuery())){
			return $this->fail();
		}
		
		if(!$this->query($this->createResearch())){
			return $this->fail();
		}
		
		if(!$this->query($this->createTimes())){
			return $this->fail();
		}
		
		if(!$this->query($this->createQuestion())){
			return $this->fail();
		}
		
		if(!$this->query($this->createSubject())){
			return $this->fail();
		}
		
		if(!$this->query($this->createText())){
			return $this->fail();
		}
		
		if(!$this->query($this->createTrackFixed())){
			return $this->fail();
		}
		
		if(!$this->query($this->createRights())){
			return $this->fail();
		}
		
		if(!$this->query($this->addAdmin($name,$pwd))){
			return $this->fail();
		}
		
		return true;	
	}
	
	function fail(){
		$query = "DROP DATABASE `$mdb_db`";
		$this->query($query);
		return "<h1>Installation failed!</h1>";
	}
	
	function query($query){
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			return true;					//Returns all researches, IDs and descriptions														//as a Mysql native result type.
		}
	}
	
	/*
	 * Creates the database table, tbl_answer into the database. This will store the
	 * different id-s of the answer and the time when the answer has been sent.
	 */
	function createAnswer(){
		return "CREATE TABLE `tbl_answer` (
			`answer_id`		int(10) unsigned	NOT NULL auto_increment,
			`research_id`	int(10) unsigned	NOT NULL,
			`UID`			int(10) unsigned	NOT NULL,
			`query_id`		int(10) unsigned	NOT NULL,
			`question_id`	int(10) unsigned	NOT NULL,
			`time`			datetime 	NOT NULL,
			PRIMARY KEY (`answer_id`)
		) COLLATE utf8_swedish_ci ;";
	}
	
	/*
	 * This table is used to check if the user has enough right to execute different
	 * operations. Whether he/she is a research owner or a super administrator.
	 * su_admin was binary(1) but I changed to tinyint(1) as it seemed more reasonable.
	 */
	function createAuth(){	
		return "CREATE TABLE `tbl_auth` (
			`UID`				int(10)		unsigned NOT NULL auto_increment,
			`password`			varchar(48)	NOT NULL,
			`su_admin`			tinyint(1)	unsigned NOT NULL,
			`username`			varchar(64)	NOT NULL,
			`research_owner`	tinyint(1)	unsigned NOT NULL,
			PRIMARY KEY (`UID`)
		) COLLATE utf8_swedish_ci ;";
	}
	
	/*
	 * This table is used for storing the data concerning the fixed interval researches:
	 * the time of the first query and the interval between the queries.
	 */
	function createFixed(){
		return "CREATE TABLE `tbl_fixed` (
			`fixed_id`		int(10)	unsigned NOT NULL auto_increment,
			`research_id`	int(10) unsigned NOT NULL,
			`firsttime`		time	NOT NULL,
			`interval`		time	NOT NULL,
			PRIMARY KEY (`fixed_id`)
		);";
	}
	
	/*
	 * This table stores the order of the queries in fixed interval researches.
	 */
	function createFixedTimes(){
		return "CREATE TABLE `tbl_fixed_times` (
			`fixedtime_id`	int(10) unsigned NOT NULL auto_increment,
			`research_id`	int(10) unsigned NOT NULL,
			`query_id`		int(10) unsigned default NULL,
			`fixedtime`		tinyint(3) unsigned default NULL,
			PRIMARY KEY (`fixedtime_id`)
		);";
	}
	
	/*
	 * This table would be used to log the different activites in the Cass-Q system.
	 */
	function createLog(){	
		return "CREATE TABLE `tbl_log` (
			`event_ID`		int(10)		unsigned NOT NULL auto_increment,
			`event_descr`	text		NOT NULL,
			`UID`			int(10)		unsigned NOT NULL,
			`IP`			varchar(32) NOT NULL,
			`date_time`		datetime NOT NULL,
			PRIMARY KEY (`event_ID`)
		) COLLATE utf8_swedish_ci ;";		
	}
	
	/*
	 * This table stores the media files if during the setup the files to database option
	 * was chosen.
	 */
	function createMedia(){	
		return "CREATE TABLE `tbl_media_answer` (
			`media_id`	int(10)	unsigned NOT NULL auto_increment,
			`answer_id`	int(10)	unsigned NOT NULL,
			`media`		longblob,
			`filepath`	varchar(80) default NULL,
			`filename`	varchar(40) default NULL,
			PRIMARY KEY (`media_id`)
		) COLLATE utf8_swedish_ci ;";
	}
	
	/*
	 * Create table for storing numerical answers.
	 */
	function createNum(){	
		return "CREATE TABLE `tbl_num_answer` (
			`num_id`	int(10) unsigned NOT NULL auto_increment,
			`answer_id`	int(10) unsigned NOT NULL,
			`num`		int(10) NOT NULL,
			PRIMARY KEY (`num_id`)
		) ;";
	}
	
	/*
	 * Creates a table to store options.
	 */
	function createOption(){	
		return "CREATE TABLE `tbl_option` (
			`option_id`		int(10)	unsigned NOT NULL auto_increment,
			`question_id`	int(10)	unsigned NOT NULL,
			`option`		varchar(255) NOT NULL,
			`superOf`		int(10)	NOT NULL,
			`number`		int(2)	unsigned NOT NULL,
			PRIMARY KEY	(`option_id`)
		) COLLATE utf8_swedish_ci ;";
	}
	
	/*
	 * Creates a table to store private variables.
	 */
	function createPrivar(){	
		return "CREATE TABLE `tbl_privar` (
			`var_id`		int(10) unsigned NOT NULL auto_increment,
			`privateVar`	varchar(50) NOT NULL,
			`subject_id`	int(10) unsigned NOT NULL,
			`number`		int(10) NOT NULL,
			PRIMARY KEY  (`var_id`)
		) COLLATE utf8_swedish_ci;";
	}
	
	/*
	 * Creates a table for storing the queries.
	 */
	function createQuery(){	
		return "CREATE TABLE `tbl_query` (
			`query_id`		int(10) unsigned NOT NULL auto_increment,
			`research_id`	int(10) unsigned NOT NULL,
			`xml_file`		mediumtext,
			`locked`		int(10) unsigned NOT NULL,
			`name` varchar(50) NOT NULL,
			`visualize` tinyint(1),
			PRIMARY KEY  (`query_id`)
		) COLLATE utf8_swedish_ci ;"; // `locked` should be as big as UID because it locks to that. Changed 10.02.2009 by Ivan
	}
	
	/*
	 * Creates a table to store the times of the queries when they are sent.
	 */
	function createTimes(){
		return "CREATE TABLE `tbl_query_times` (
			`qtime_id`		int(10) unsigned NOT NULL auto_increment,
			`research_id`	int(10) unsigned NOT NULL,
			`query_id`		int(10) unsigned default NULL,
			`qtime`			varchar(5) default NULL,
			PRIMARY KEY (`qtime_id`)
		) COLLATE utf8_swedish_ci;";
	}
	
	/*
	 * Creates a table to store questions.
	 */
	function createQuestion(){	
		return "CREATE TABLE `tbl_question` (
			`question_id`	int(10) unsigned NOT NULL auto_increment,
			`query_id`		int(10) unsigned NOT NULL,
			`question`		varchar(255) NOT NULL,
			`question_type`	tinyint(3) unsigned NOT NULL,
			`number`		tinyint(3) unsigned NOT NULL,
			`category`		tinyint(3) unsigned NOT NULL,
			PRIMARY KEY (`question_id`)
		) COLLATE utf8_swedish_ci ;";
	}
	
	/*
	 * Creates a table to store inforamation about the researches.
	 */
	function createResearch(){
		return "CREATE TABLE `tbl_research` (
			`research_id`		int(10)		unsigned	NOT NULL auto_increment,
			`research_name`		varchar(60) 			NOT NULL,
			`research_descr`	varchar(255)			NOT NULL,
			`data_collection_method` tinyint(1)	unsigned NOT NULL,
			`startTime`			date	default NULL,
			`endTime`			date	default NULL,
			`queriesPerDay`		int(2) 	unsigned	default NULL,
			`locked` 			varchar(7)	NULL,
			`created`			timestamp	NULL default CURRENT_TIMESTAMP,
			PRIMARY KEY  (`research_id`),
			UNIQUE KEY `research_name` (`research_name`)
		) COLLATE utf8_swedish_ci ;";
	}
	
	/*
	 * Creates a table for respondents to store BT ids and that which research
	 * they belong to.
	 */
	function createSubject(){	
		return "CREATE TABLE `tbl_subject` (
			`subject_id`	int(10) unsigned NOT NULL auto_increment,
			`UID`			int(10) unsigned NOT NULL,
			`research_id`	int(10) unsigned NOT NULL,
			`bt_id`			varchar(12) default NULL,
			`active`		tinyint(1) default NULL,
			PRIMARY KEY (`subject_id`)
		) COLLATE utf8_swedish_ci ;";
		
	}

	/*
	 * Creates table for text answers.
	 */
	function createText(){	
		return "CREATE TABLE `tbl_text_answer` (
			`text_id`	int(10) unsigned NOT NULL auto_increment,
			`answer_id` int(10) unsigned NOT NULL,
			`text`		text NOT NULL,
			PRIMARY KEY  (`text_id`)
		) COLLATE utf8_swedish_ci ;";
	}
	
	/*
	 * This table helps to keep a track on the queries answered in fixed interval
	 * researches.
	 */
	function createTrackFixed(){
		return "CREATE TABLE `tbl_track_fixed` (
			`subject_id`	int(10) unsigned NOT NULL,
			`timeanswered`	tinyint(3) unsigned NULL,
			`lastanswered`	timestamp ON UPDATE CURRENT_TIMESTAMP NULL default CURRENT_TIMESTAMP,
			PRIMARY KEY (`subject_id`)
		) ;";
	}
	
	/*
	 * Creates a table for storing user rights.
	 */
	function createRights(){	
		return "CREATE TABLE `tbl_user_rights` (
			`research_id`	int(10) unsigned NOT NULL,
			`UID`			int(10) unsigned NOT NULL,
			`subject`		tinyint(1) unsigned NOT NULL,
			`researcher`	tinyint(1) unsigned NOT NULL,
			`admin`			tinyint(1) unsigned NOT NULL
		) ;";
	}
	
	/*
	 * Adds the super admin into the the tbl_auth table.
	 */
	function addAdmin($usr,$pwd){
		return "INSERT INTO `tbl_auth` VALUES (NULL,'$pwd',1,'$usr',0); ";
	}

}
?>