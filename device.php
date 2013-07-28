<?php


class Device {
	public $deviceuid;
	private $created;
	private $active;
	
	function __construct() {
		
	}
	
	static public function isRegistered($deviceuid='')
	{
	return FALSE;	
	}
	
	public function registerDevice() {
		
	}
	private function isActive() {
		
	}
}


$device = new Device();
$isreg=$device::isRegistered("sdfsdfwer24");
$out=($isreg)?"reg":"not reg";

echo $out;
?>