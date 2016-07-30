<?php

// LOAD LIST OF USER CREATED FUNCTIONS FROM FUNCTIONS DIRECTORY (NOTE, ONLY IF A FUNCTIONS SUBDIR EXISTS)
if (file_exists("tools/functions")) {
	if ($_SERVER['REMOTE_ADDR'] == "1130.39.96.20") {	
		$functionDir = opendir("tools/functionsNew");
	} else {
		$functionDir = opendir("tools/functions");
	}
	while ($file = readdir($functionDir)) {
		if (strstr($file,"function_")) {
			$function = $file;
			$function = str_replace("function_","",$function);
			$function = str_replace(".php","",$function);
			$approvedFunctions[] = $function;
		}
	}
}

/*
function recursiveFunction($function,$array) {
	$responseArray = array();
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			$responseArray[$key] = recursiveFunction($function,$value);
		} else {
			$responseArray[$key] = $function($value);
		}
	}
	return $responseArray;
}
*/

function mysqlQuery($query,$filename=__FILE__,$lineNum=__LINE__,$errorMessage='Query encountered an error',$singleResult=false) {
	global $newQueryLog;
	global $siteRoot;
	$SQLResult = array();
	$queryStart = microtime(true);
	$result = mysqli_query($dbc,$query) or die ($errorMessage . ": " . mysql_error());
	if (substr($query,0,6) == "SELECT") {
		if (mysqli_affected_rows($dbc)) {
			while ($row = mysqli_fetch_array($GLOBALS['dbc'],$result)) {
				$SQLResult[] = $row;
			}
		}
	}
	$queryEnd = microtime(true);
	$queryRunTime = $queryEnd - $queryStart;
	$newQueryLog[] = array (
						 "query"	=>	$query,
						 "runtime"	=>	$queryRunTime,
						 "filename"	=>	str_replace($siteRoot,"",$filename),
						 "lineNum"	=>	$lineNum,
	);
	if ($singleResult) {
		$response = $SQLResult['0'];
	} else {
		$response = $SQLResult;
	}
	return $response;
}

?>