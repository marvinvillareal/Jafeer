<?php
date_default_timezone_set('Asia/Dubai');

function errorJson($msg){
	print json_encode(array('error'=>$msg));
	exit();
}

function log_msg($msg) {
	$fp = fopen("log.txt", "a");
	fwrite($fp, $msg . "\n");
	fclose($fp);
}

function preregister($args) {
	//check if device exists
	$registered;

  if (!empty($args)) {
	 	$register = query("SELECT * FROM devices WHERE deviceuid='%s' limit 1", $args['deviceuid']);
		if (count($register['result'])>0) {
				if ($register['result'][0]['active']==1) {
				// active
				print json_encode(array('active'=>1));
				}else {
				// inactive
					$user = query("SELECT * FROM user WHERE mobileNumber='%s' limit 1", $args['mobile']);
					if (count($user['result'])>0) {
					// mobile exist
						$device = query("SELECT * FROM devices WHERE id='%s' limit 1", $user['result'][0]['devices_id']);
						if($device['result'][0]['active']==1) {
							// mobile exist,device active
							print json_encode(array('mobile_exist'=>1,'active'=>1));
						} else {
							// mobile exist,device inactive
							if(sendCodeSms($args['regcode'],$args['mobile'])) {
								print json_encode(array('mobile_exist'=>1,'active'=>0,'codesent'=>1));
							} else {
								errorJson('send sms failed.');
							}
						}
						
					} else {
					// mobile dont exist
						if(sendCodeSms($args['regcode'],$args['mobile'])) {
							print json_encode(array('mobile_exist'=>0,'active'=>0,'codesent'=>1));
						} else {
							errorJson('send sms failed.');
						}
					}	
						
				}
			
	 	} else {
	 		// insert new deviceuid into devices table
	 		$result = query("INSERT INTO devices 
	 		VALUES(NULL,'%s','%s','%s','%s',0,NOW(),NULL)"
	 		, $args['deviceuid']
	 		, $args['devicename']
	 		, $args['devicemodel']
	 		, $args['deviceversion']
			);
			if (!$result['error']) {
			// device insert success
					$user = query("SELECT * FROM user WHERE mobileNumber='%s' limit 1", $args['mobile']);
					if (count($user['result'])>0) {
					// mobile exist
						$device = query("SELECT * FROM devices WHERE id='%s' limit 1", $user['result'][0]['devices_id']);
						if($device['result'][0]['active']==1) {
							// mobile exist,device active
							print json_encode(array('mobile_exist'=>1,'active'=>1));
						} else {
							// mobile exist,device inactive
							if(sendCodeSms($args['regcode'],$args['mobile'])) {
								print json_encode(array('mobile_exist'=>1,'active'=>0,'codesent'=>1));
							} else {
								errorJson('send sms failed.');
							}
						}
						
						
					} else {
					// mobile dont exist
						if(sendCodeSms($args['regcode'],$args['mobile'])) {
							print json_encode(array('mobile_exist'=>0,'active'=>0,'codesent'=>1));
						} else {
							errorJson('send sms failed.');
						}
					}
	
				
		 	} else {
		 	// device insert failed
		 		errorJson('device registration failed.');
			}
		}
 
	}


}


function register($mobile, $udid, $firstName, $lastName, $fatherName, $familyName, $nationalId, $nationality, $regCode) {
	//check if device exists
	$register = query("SELECT * FROM user WHERE mobileNumber='%s' limit 1", $mobile);
	if (count($register['result'])>0) {
		errorJson('This device already registered on the server');
	} else {
		//try to register the user
		$deviceid = query("SELECT * FROM devices WHERE deviceuid='%s' limit 1", $udid);
		$deviceid=($deviceid==null)?0:$deviceid['result'][0]['id'];

		$result = query("INSERT INTO user(mobileNumber, udid,firstName,lastName,fatherName,familyName,national_id,nationality,regCode,devices_id) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')"
		, $mobile, $udid, $firstName, $lastName, $fatherName, $familyName, $nationalId, $nationality, $regCode,$deviceid);
		if (!$result['error']) {
			//success
			$update = query("UPDATE devices SET active=1 WHERE id='%s'", $deviceid);
				if (!$update['error']) {
					print json_encode(array('successful'=>1));
				} else {
					errorJson('Update failed.');
				}
			//print json_encode(array('successful'=>1));
			
		} else {
			//error
			errorJson('Registration failed');
		}
		
		
		
	}
}

function unregister($mobile, $udid) {
	$updatedevice = query("UPDATE devices SET active=0 WHERE deviceuid='%s'", $udid);
	$update = query("UPDATE user SET active=0 WHERE udid='%s'", $udid);
	if (!$update['error']) {
		print json_encode(array('successful'=>1));
	} else {
		errorJson('Update failed.');
	}
}

function update($mobile, $udid) {
	$deviceid = query("SELECT * FROM devices WHERE deviceuid='%s' limit 1", $udid);
	$deviceid=$deviceid['result'][0]['id'];
	$update = query("UPDATE user SET udid='%s',active=1,devices_id='%s' WHERE mobileNumber='%s'", $udid, $mobile, $deviceid);
	if (!$update['error']) {
		$updatedevice = query("UPDATE devices SET active=1 WHERE id='%s'", $deviceid);
			$valid = query("SELECT * FROM user WHERE udid='%s' limit 1", $udid);
			if (count($valid['result'])>0) {
			
			print json_encode(array('mobilenumber'=>$valid['result'][0]['mobileNumber']
					,'firstname'=>$valid['result'][0]['firstName']
					,'lastname'=>$valid['result'][0]['lastName']
					,'nationalid'=>$valid['result'][0]['national_id']
					,'nationality'=>$valid['result'][0]['nationality']
					,'active'=>$valid['result'][0]['active']
										));
				
			} else {
			errorJson('invalid device.');
			}
	
	
	} else {
		errorJson('Update failed.');
	}
}

function activate($udid) {
	$update = query("UPDATE user SET active=1 WHERE udid='%s'", $udid);
	if (!$update['error']) {
		
			$valid = query("SELECT * FROM user WHERE udid='%s' limit 1", $udid);
			if (count($valid['result'])>0) {
			
			print json_encode(array('regcode'=>$valid['result'][0]['regCode']
					,'mobilenumber'=>$valid['result'][0]['mobileNumber']
					,'firstname'=>$valid['result'][0]['firstName']
					,'lastname'=>$valid['result'][0]['lastName']
					,'fathername'=>$valid['result'][0]['fatherName']
					,'familyname'=>$valid['result'][0]['familyName']
					,'nationalid'=>$valid['result'][0]['national_id']
					,'nationality'=>$valid['result'][0]['nationality']
					,'active'=>$valid['result'][0]['active']
										));
				
			} else {
			errorJson('invalid device.');
			}
	
	
	} else {
		errorJson('Update failed.');
	}
}

function validate($regID) {
	//check if code is equal
	$dateNow = new DateTime();
	$update = query("UPDATE registrationCode SET registered=1,dateReceived='%s' WHERE id='%s'", $dateNow, $regID);
	if (!$update['error']) {
		print json_encode(array('successful'=>1));
	} else {
		errorJson('validation failed.');
	}
}

function insertCode($code) {
	$dateNow = new DateTime();
	$result = query("INSERT INTO registrationCode(code, dateCreated) VALUES('%s','%s')", $code, $dateNow);
	if (!$result['error']) {
		//success
		$registerID = query("SELECT id FROM registrationCode WHERE code='%s' limit 1", $code);
		if (!$registerID['error']) {
			return $registerID['result'][0]['id'];
		} else {
			return 0;
		}
		
	} else {
		//error
		return 0;
	}
	
}

function sendCodeSms($code,$receiver) {
	
	$status='Send';
	// send to ozekisms dbase
				$result2 = ozekiquery("INSERT INTO ozekimessageout_copy(sender, receiver, msg, status) 
									VALUES('%s','%s','%s','%s')",
									$sender, $receiver, $code, $status);	

								
				//print $sender;

				if (!array_key_exists('error',$result2)) { // <-- will check if error in ozekiquery
					//success
					return true;
				} else {
					//error
					return false;
				}

}

function isValid($udid) {
	//check if code is equal
	//CAST(EmailVerified AS unsigned integer) AS EmailV
//	$valid = query("SELECT user.mobileNumber,user.firstName,user.lastName,CAST(registrationCode.registered AS unsigned integer) AS registered,registrationCode.code FROM user INNER JOIN registrationCode ON user.regCodeID = registrationCode.id WHERE udid='%s' limit 1", $udid);
	$valid = query("SELECT * FROM devices WHERE deviceuid='%s' limit 1", $udid);


if (count($valid['result'])>0) {
	
	print json_encode(array('udidexist'=>1
			,'active'=>$valid['result'][0]['active']
								));			
		
	} else {
	
			errorJson('This device can register');
			//else
			//errorJson('Authorization required');
	}
}

function logout() {
	$_SESSION = array();
	session_destroy();
}

function upload($udid, $photoData, $title) { 
	//check if a user id is passed
	$user = query("SELECT id FROM user WHERE udid='%s' limit 1", $udid);
	if (count($user['result'])>0) {
		//check if there was no error during the file upload
		if ($photoData['error']==0) {
						
				$uploaddir = 'upload/';
    			$file = basename($photoData['name']);
    			$uploadfile = $uploaddir . $file;

    			if (move_uploaded_file($photoData['tmp_name'], $uploadfile)) {
        			$imgContent = file_get_contents($tmpName);
        			$blob = addslashes($imgContent);
        			
					
					
					
					$result = query("UPDATE user SET photo='%s' WHERE id='%s'", $blob, $user['result'][0]['id']);
					if (!$result['error']) {
				
						print json_encode(array('successful'=>1));
				
				
					} else {
						errorJson('Upload database problem.'.$result['error']);
					}
					
					
					
					
					
					
    			}else {
				errorJson('Upload image error');
				}
					
				
			
		} else {
			errorJson('Upload malfunction');
		}
	}else {
			errorJson('no user');
		}
}



function uploads($udid, $photoData, $title) {
	if (move_uploaded_file($photoData['tmp_name'], "upload/photo.jpg")) {
				//file moved, all good, generate thumbnail
				//thumb("upload/".$title.".jpg", 90);
				print json_encode(array('successful'=>1));
				} else {
					§errorJson('Upload on server problem');
				}
}


?>