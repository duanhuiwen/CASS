<?php
abstract class FileIO{
	var $path;		//Includes the info where to store the folders that store the media
	var $voice;		//The name of the folder to store voice files(typenum = 3)
	var $video;		//The name of the folder to store video files(typenum = 8)
	var $img;		//The name of the folder to store image files(typenum = 7)
	var $server;
	var $serverType;
	var $serverPwd;
	var $serverUsn;
	var $dbName;
	var $mdb2;
	
	public function __construct(){
		include "../settings/dbsettings.php";
		//for writing in database
		$this->server=$mdb_server;
		$this->serverType=$mdb_type;
		$this->serverPwd=$mdb_passwd; 
		$this->serverUsn=$mdb_usn;
		$this->dbName=$mdb_db;
		$this->mdb2=$mdb2;
		//folders for writing in disc
		$this->path = $mediaFolderPath;
 		$this->img = $pictureFolder;
 		$this->video = $videoFolder;
 		$this->voice = $soundFolder;
	}
	
	abstract function MediaWrite($file,$type,$answer_id,$name);
	abstract function MediaRead($type,$answer_id);
	abstract function writeQueryAnswerTxt($query_id,$ext);
	abstract function writeResearchAnswerTxt($research_id);
	abstract function zipMediaFiles($research_id);
	abstract function addFileToZip($zipfile,$data,$zipEntryName);

}//end of class
?>