<?php

if ((strstr($_SERVER['SERVER_NAME'],"dev.gberatings.com")) && ($_SERVER['REMOTE_ADDR'] != "70.186.180.69") && ($_SERVER['REMOTE_ADDR'] != "2620:105:b000:7104:224:21ff:fe53:b5ff")) {
	header("Location: http://www.gberatings.com");
	exit();	
}

if (!strstr($_SERVER['SERVER_NAME'],"www.gberatings.com")) {
	header("Location: http://www.gberatings.com" . $_SERVER['REQUEST_URI']);
	exit();	
}

//$siteDown = true;
if ($siteDown) {
	echo "Site temporarily down for maintenance: please check back later";
	exit();
}

ob_start();
session_start();

require("tools/mysqlConnect.php");
require("tools/functions.php");
require("tools/functions/function_navMenu.php");
require("tools/config.php");
require("tools/redirects.php");

foreach ($_POST as $key => $value) {
	$_POST[$key] = mysql_real_escape_string($value);
}

$urlRequested = $_SERVER['PATH_INFO'];

if (substr($urlRequested,-1) == "/") {
	$urlRequested = substr($urlRequested,0,-1);
}
	
$query_getRoot = "SELECT id,permissions FROM structure WHERE root='1' ORDER BY id ASC LIMIT 1";
$result_getRoot = mysqli_query($dbc,$query_getRoot) or die ("Could not get root id: " . mysql_error());
if (mysqli_affected_rows($dbc)) {
	$row_getRoot = mysqli_fetch_array($result_getRoot);
	$rootId = $row_getRoot['id'];
	$rootPermissions = $row_getRoot['permissions'];
}

$query_get404 = "SELECT id FROM structure WHERE subOf='$rootId' AND name='404' ORDER BY id ASC LIMIT 1";
$result_get404 = mysqli_query($dbc,$query_get404) or die ("Could not get 404 id: " . mysql_error());
if (mysqli_affected_rows($dbc)) {
	$row_get404 = mysqli_fetch_array($result_get404);
	$notFound404Id = $row_get404['id'];
}

if ($urlRequested) {
	
	$urlSections = explode("/",substr($urlRequested,1));
	
	if (($urlSections['0'] == "conf") && ($urlSections['1'] == "index.php") && (strstr($_SERVER['QUERY_STRING'],"listschools="))) {
		$conf = str_replace("listschools=","",$_SERVER['QUERY_STRING']);
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: /conf/" . $conf . "/");
		exit();
	}
	
	foreach ($urlSections as $key => $value) {
		$urlSections[$key] = mysqli_real_escape_string($dbc,$value);
	}

	$pageId = $rootId;
	
	for ($section=0;$section<sizeof($urlSections);$section++) { // CHECK FOR EXISTANCE OF CALLED PAGE

		$parentClause = " AND subOf='" . $pageId . "'";
		
		$query_checkSection = "SELECT id,name,nameDisplay,type,url,permissions FROM structure WHERE name='" . $urlSections[$section] . "' $parentClause";
		$result_checkSection = mysqli_query($dbc,$query_checkSection) or die ("Could not check for requested section: " . mysql_error());
		if (mysqli_affected_rows($dbc)) {
			$row_checkSection = mysqli_fetch_array($result_checkSection);
	
			$pageFound = true;
			if ($section == 0) {
				$breadcrumbs = '<a href="' . $breadCrumbPath . '">' . $row_checkSection['nameDisplay'] . '</a>';
				$topLevelId = $row_checkSection['id'];
				$topLevelActive = $row_checkSection['name'];
			} else {
				$interiorLevelId = $row_checkSection['id'];
				$breadcrumbs .= '&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;<a href="' . $breadCrumbPath . '">' . $row_checkSection['nameDisplay'] . '</a>';
			}
			$pageId = $row_checkSection['id'];
			$sourcePermissions = $row_checkSection['permissions'];
			if ($section <= 1) {
				$pageHeader = $row_checkSection['nameDisplay'];
			} elseif (($section == "2") || ($section == "3")) {
				if ($row_checkSection['nameLong']) {
					$pageSubHeader = $row_checkSection['nameLong'];
				} else {
					$pageSubHeader = $row_checkSection['nameDisplay'];
				}
			}
			if ($section == 1) {
				$level2Name = $row_checkSection['name'];
			} elseif ($section == 2) {
				$level3Name = $row_checkSection['name'];
			} elseif ($section == 3) {
				$level4Name = $row_checkSection['name'];
			}
			$pageActive = $row_checkSection['name'];
	
			if ($row_checkSection['type'] == "link") {
				$isLink = true;
				$url = $row_checkSection['url'];
			} else {
				unset($isLink,$url);
			}
	
			if (stristr($urlSections[$section],"=")) {
				$varsPresent++;
				break;
			}
			
			if (($pageActive == "past") && (strlen($urlSections[$section+1]) == "4") && (is_numeric($urlSections[$section+1])) && ($urlSections[$section+2])) {
				$year = $urlSections[$section+1];
				$type = $urlSections[$section+2];
				break;
			}
			
			if (($pageActive == "conf") && ($urlSections[$section+1])) {
				$conf = $urlSections[$section+1];
				$confRedirects = array (
									"pac10"		=>	"pac12",
				);
				if ($confRedirects[$conf]) {
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: /conf/" . $confRedirects[$conf]);
					exit();
				}			
				break;
			}
			
		} else {
			$pageId = $notFound404Id;
			$notFound404++;
			break;
		}
		
	}
	
} else {
	$pageId = $rootId;
	$sourcePermissions = $rootPermissions;
}

if ($sourceId == $notFound404Id) {
	$notFound404++;	
}

if ($isLink) {
	header("Location: " . $url);
	exit();
}

if (($notFound404) && ($redirectArray[$urlRequested])) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: " . $redirectArray[$urlRequested]);
	exit();
} elseif ($notFound404) {
	header("HTTP/1.1 404 Not Found");
	if ((!strstr($urlRequested,"/mirrors")) && ($urlRequested != "/404") && (!strstr($_SERVER['REMOTE_HOST'],"googlebot")) && (!strstr($_SERVER['REMOTE_HOST'],"ceng-compy386")) && (!strstr($_SERVER['REMOTE_HOST'],"its-ncircle"))) {
		$query_check = "SELECT id FROM request404 WHERE request='$urlRequested' LIMIT 1";
		$result_check = mysqli_query($dbc,$query_check) or die ("Could not check for this request: " . mysql_error());
		if (!mysqli_affected_rows($dbc)) {
			$query_add404 = "INSERT INTO request404 (request) VALUES ('$urlRequested')";
			$result_add404 = mysqli_query($dbc,$query_add404) or die ("Could not add 404 to db: " . mysql_error());
		}
	}
}

if ($pageId) {
	$queryLog[] = $query_getSource = "SELECT content FROM source WHERE pageId='$pageId'";
	$result_getSource = mysqli_query($dbc,$query_getSource) or die ("Could not get source: " . mysql_error());
	if (mysqli_affected_rows($dbc)) {
		$row_getSource = mysqli_fetch_array($result_getSource);
		$source = $row_getSource['content'];
		
		if (preg_match_all("/\[\[\[\[.+\]\]\]\]/",$source,$functions)) {
			foreach ($functions[0] as $function) {
				$function = str_replace("[[[[","",$function);
				$function = str_replace("]]]]","",$function);
				$functionName = explode("(",$function);
				if (($approvedFunctions) && (in_array($functionName['0'],$approvedFunctions))) {
					if ($_SERVER['REMOTE_ADDR'] == "1130.39.96.20") {
						require("tools/functionsNew/function_" . $functionName['0'] . ".php");
					} else {
						require("tools/functions/function_" . $functionName['0'] . ".php");
					}
					eval('$functionReturn = ' . $function . ';');
					$searchString = "[[[[" . $function . "]]]]";
					$source = str_replace($searchString,$functionReturn,$source);
				};
			}
		}
		
	}
}

require("tools/source/source.php");
//require("tools/source/pageEdit.php");

//}

?>