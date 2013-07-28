<?php
header("Content-Type: application/json");
//session_start();
require("lib.php");
require("api.php");


switch ($_REQUEST['command']) {
	case "preregister":
		preregister($_REQUEST); 
		break;
	
	case "register":
		register($_REQUEST['mobile'], $_REQUEST['udid'], $_REQUEST['firstname'], $_REQUEST['lastname'], $_REQUEST['fathername'], $_REQUEST['familyname'], $_REQUEST['nationalid'], $_REQUEST['nationality'], $_REQUEST['regcode']); 
		break;
	
	case "unregister":
		unregister($_REQUEST['mobile'], $_REQUEST['udid']); 
		break;
	
	case "update":
		update($_REQUEST['mobile'], $_REQUEST['udid']); 
		break;
	
	case "activate":
		activate($_REQUEST['udid']); 
		break;
	
	case "register_trial":
		register_trial($_REQUEST['mobile'], $_REQUEST['udid'], $_REQUEST['firstname'], $_REQUEST['lastname']); 
		break;
	
	case "validate":
		validate($_REQUEST['regID']); 
		break;
		
	case "isvalid":
		isvalid($_REQUEST['udid']); 
		break;
			
	case "logout":
		logout();
		break;
		
	case "upload":
		{
			//upload($_REQUEST['udid'], $_FILES['file'], $_REQUEST['title']);
			
			//check if a user id is passed
			$user = query("SELECT id FROM user WHERE udid='%s' limit 1", $_REQUEST['udid']);
			if (count($user['result'])>0) {
				//check if there was no error during the file upload
				if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
					
					// Temporary file name stored on the server  
		      		$tmpName  = $_FILES['file']['tmp_name'];  
		      		$uploaddir = 'upload/photo.jpg';
					
					// Read the file  
					$fp = fopen($tmpName, 'r');  
					$data = fread($fp, filesize($tmpName));  
					$data = addslashes($data);  
					fclose($fp);  
				        
					// get the id from db
					$id= $user['result'][0]['id'];
				
					// Create the query and insert  
					// into our database.  
					$query = "UPDATE user SET photo='" .$data ."' WHERE id =".$id.";";
								      
					//execute and fetch the results
					$result = mysqli_query($link, $query);
					if (mysqli_errno($link)==0 && $result) {
						print json_encode(array('successful'=>1));
					}else {
						errorJson('Upload database problem.'.$result['error']);
					}
			  
			  
				} else {
					errorJson('Upload file error');
				}	
				
		
			}else {
					errorJson('no user');
			}
		
		}
		break;



}

//exit();
		
?>