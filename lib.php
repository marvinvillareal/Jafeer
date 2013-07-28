<?php

//setup db connection
$link = mysqli_connect("localhost","root","samsoft123") or die(json_encode(array("error" => "Could not connect to the database!")));
mysqli_select_db($link, "jafeer") or die(json_encode(array("error" => "Could not switch to database!")));
$link2 = mysqli_connect("localhost","root","samsoft123") or die(json_encode(array("error" => "Could not connect to the database!")));
mysqli_select_db($link2, "ozekisms") or die(json_encode(array("error" => "Could not switch to database!")));

//executes a given sql query with the params and returns an array as result
function query() {
	global $link;
	$debug = false;
	
	//get the sql query
	$args = func_get_args();
	$sql = array_shift($args);

	//secure the input
	for ($i=0;$i<count($args);$i++) {
		$args[$i] = urldecode($args[$i]);
		$args[$i] = mysqli_real_escape_string($link, $args[$i]);
	}
	
	//build the final query
	$sql = vsprintf($sql, $args);
	
	if ($debug) print $sql;
	
	//execute and fetch the results
	$result = mysqli_query($link, $sql);
	if (mysqli_errno($link)==0 && $result) {
		
		$rows = array();

		if ($result!==true)
		while ($d = mysqli_fetch_assoc($result)) {
			array_push($rows,$d);
		}
		
		//return json
		return array('result'=>$rows);
		
	} else {
	
		//error
		return array('error'=>'Database error');
	}
}

function ozekiquery() {
	global $link2;
	$debug = false;
	
	//get the sql query
	$args = func_get_args();
	$sql = array_shift($args);

	//secure the input
	for ($i=0;$i<count($args);$i++) {
		//$args[$i] = urldecode($args[$i]);
		$args[$i] = mysqli_real_escape_string($link2, $args[$i]);
	}
	
	//build the final query
	$sql = vsprintf($sql, $args);
	
	if ($debug) print $sql;
	
	//execute and fetch the results
	$result = mysqli_query($link2, $sql);
	if (mysqli_errno($link2)==0 && $result) {
		
		$rows = array();

		if ($result!==true)
		while ($d = mysqli_fetch_assoc($result)) {
			array_push($rows,$d);
		}
		
		//return json
		return array('result'=>$rows);
		
	} else {
	
		//error
		return array('error'=>'Database error');
	}
}



?>