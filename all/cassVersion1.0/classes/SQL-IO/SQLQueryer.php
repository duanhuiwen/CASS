<?php
abstract class SQLQueryer{
		var $server;
		var $serverType;
		var $serverPwd;
		var $serverUsn;
		var $dbName;
		var $mdb2;

	function __construct(){
		include "../settings/dbsettings.php";
		$this->server=$mdb_server;
		$this->serverType=$mdb_type;
		$this->serverPwd=$mdb_passwd; 
		$this->serverUsn=$mdb_usn;
		$this->dbName=$mdb_db;
		$this->mdb2=$mdb2;
	}
	
	abstract function getInfo($id);
	
	abstract function createTables2DB();
	
	static function listAllResearch(){				//Documentation goes here NOT IN USE
		include ("../settings/dbsettings.php");
		$query= "SELECT `research_name`, `research_id`, `research_descr` FROM `tbl_research`;";
		$result = $mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			return $native_result;						//Returns all researches, IDs and descriptions
															//as a Mysql native result type.
		}	
	}													//end function
	
	static function listAllUsers()	{				//Documentation goes here NOT IN USE
		include ("../settings/dbsettings.php");
		$query= "SELECT * FROM `tbl_auth` ORDER BY `username` ASC;";
		$result = $mdb2->query($query) or die('An unknown error occurred while updating the data');
		if(MDB2::isError($result)){
			return false;
		}else{
			$native_result = $result->getResource();
			return $native_result;						//Returns all researches, IDs and descriptions
															//as a Mysql native result type.
		}	
	}													//end function
	
	static function getResearchUsers($type="all", $rID="all"){
		include "../settings/dbsettings.php";
		//TODO: This can be done when all the table structures are finally set...
		//Maybe we won't need this, as in most cases you'd only need a specific set of users anyways...
	}//end function

}
?>