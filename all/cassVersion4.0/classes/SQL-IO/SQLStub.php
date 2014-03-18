<?php
class QuestionSQLQueryer extends SQLQueryer{



	


	
	function setSQLInsertQueryFunctionStub(){					//Documentation goes here
		$query= "";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while updating the data');
	if(MDB2::isError($result)){
		return false;
	}else{
		return true;
	}	
	}													//end function
		
		
		
	}
	function getSQLInsertQueryFunctionStub(){					//Documentation goes here
		$query= "";
		$result = $this->mdb2->query($query) or die('An unknown error occurred while checking the data');
		if(MDB2::isError($result)){
			return "SelectError";
		}else{
			$native_result = $result->getResource();
			$row=Mysql_Fetch_Row($native_result);
			return $row[0];				//What does it return?.

		}
	}									//End function	

		
	
	
	
	
	

	
	
	function createTables2DB(){
		//The table creation function goes here
		if($temp==false ||$temp2==false ){
			return false;
		}else{
			return true;
		}
	}													//end function
														//end class
?>