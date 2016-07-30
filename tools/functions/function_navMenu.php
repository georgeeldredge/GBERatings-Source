<?php

function navMenu($pageActive) {
	
	$query_getMenu = "SELECT * FROM structure WHERE menuDisplay='1' ORDER BY priority DESC";
	$result_getMenu = mysqli_query($GLOBALS['dbc'],$query_getMenu) or die ("Could not get menu: " . mysql_error());
	if (mysqli_affected_rows($GLOBALS['dbc'])) {
		
		$response .= "\r\n" . '<ul id="nav">';
		
			if (!$pageActive) {
				$homeActive = ' class="active"';
			}
			
			$response .= "\r\n" . '<li><a ' . $homeActive . ' href="/">Overall Ratings</a></li>';
		
			while ($row_getMenu = mysqli_fetch_array($result_getMenu)) {			
				if ($row_getMenu['name'] == $pageActive) {
					$active = 'class="active"';
				} else {
					unset($active);
				}
    	    	$response .= "\r\n" . '<li><a ' . $active . ' href="/' . $row_getMenu['name'] . '/">' . $row_getMenu['nameDisplay'] . '</a></li>';
			}
		
			if (($_SERVER['REMOTE_ADDR'] == "130.39.28.13") OR ($_SERVER['REMOTE_ADDR'] == "130.39.96.20") OR ($_SERVER['REMOTE_ADDR'] == "68.111.53.251")) { // ONLY SHOW THE SITEADMIN LINK TO FRIENDLY MACHINES - OTHER MACHINES WILL HAVE TO MANUALLY ENTER SITEADMIN
				$response .= "\r\n" . '<li><a href="/cms/">CMS</a></li>';
			}
			
		$response .= "\r\n" . '</ul>';
		
	}
	
	return $response;	
	
}

?>