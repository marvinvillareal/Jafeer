<?php
   require("lib.php");
   
	header("Content-Type: image/png");
   $imagedata;
 		  $user = query("SELECT * FROM user WHERE udid='%s' limit 1", $_REQUEST['udid']);
			
			if (count($user['result'])>0) {
				
				 $imagepath=$user['result'][0]['photo'];
				
        			if($imagepath){
        				echo $imagepath;
						         				
					} else {
						$nophotoDir = 'upload/no_photo.png';
       					$imagedata = file_get_contents($nophotoDir);
						echo $imagedata;			
        			}
			
			 
				
			} else {
				// Read the file  
 
			}


?>