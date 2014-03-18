<?

		$media="../MediaFiles/";
		//Folder for each media file

		$media.="Research_121/";
		if(!is_dir($media)){
			mkdir($media,0755);
		}
		$media.= "Query_12/";
		if(!is_dir($media)){
			mkdir($media,0755);
		}	


?>