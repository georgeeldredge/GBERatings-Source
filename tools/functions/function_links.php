<?php

function links() {
	
	$query_getLinks = "SELECT * FROM links WHERE deleted=0 ORDER BY name ASC";
	$result_getLinks = mysqli_query($GLOBALS['dbc'],$query_getLinks) or die ("Could not get links: " . mysql_error());
	if (mysqli_affected_rows($GLOBALS['dbc'])) {
		
		$response .= "\r\n" . '<ul id="links">';
		
			while ($row_getLinks = mysqli_fetch_array($result_getLinks)) {

				$response .= "\r\n" . '<li class="link"><a href="' . $row_getLinks['link'] . '" target="_blank">' . $row_getLinks['name'] . '</a></li>';
				if ($row_getLinks['description']) {
					$response .= "\r\n" . '<ul class="description"><li class="description">' . $row_getLinks['description'] . '</li></ul>';
				}

			}
			
		$response .= "\r\n" . '</ul>';
		
	}
		
	return $response;
		
}

?>