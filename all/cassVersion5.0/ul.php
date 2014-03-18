<?php 
$polku = "./upload/";

    if (substr($_FILES['image']['name'], -1, 3) != 'php') 
	{
          if (move_uploaded_file($_FILES['image']['tmp_name'], $polku.$_FILES['image']['name'])) 
		  {
            echo "OK";
			echo $_POST['qid'];
			echo $_FILES['image']['name'];
          } else {
             echo "Fail";
          }        
    } 
?> 