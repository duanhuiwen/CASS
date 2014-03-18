<?php
abstract class DatabaseWriteable{
	private $id;
	private $inDB;		//Boolean value, is the class in the database or not
	private $dbq;		//Coresponding descendant of DatabaseQueryer class
	private $logger; 	//A logger object to... Log things.
	private $crypto;	//A crypto object to... *Crypt things.
	public $children;
	
	function __construct($id=0){
		$this->inDB = false;
		$this->logger = new LogSQLQueryer();
		//TODO: IMPLEMENT ENCTRYPTIONENGINE $this->crypto = new EncryptionEngine();
		//debug: echo("Kicki");
				
	}
	
	function getID(){
		return $this->id;
	}
	
}
?>