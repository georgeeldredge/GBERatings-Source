<?php

function pastMenu() {
	
	$response .= "\r\n" . '<h1>Past Final GBE Computer Ratings</h1>';

	$query_getPastMenu = "SELECT seasonIndex.season,teamIndex.teamName FROM seasonIndex LEFT JOIN teamIndex ON seasonIndex.champId=teamIndex.id WHERE seasonIndex.finished='1' ORDER BY seasonIndex.season ASC";
	$result_getPastMenu = mysqli_query($GLOBALS['dbc'],$query_getPastMenu) or die ("Could not get past menu: " . mysql_error());
	if (mysqli_affected_rows($GLOBALS['dbc'])) {
		
		$response .= "\r\n" . '<ul id="pastMenu">';
			$response .= "\r\n" . '<ul class="head">';
				$response .= "\r\n" . '<li class="year">Season</li>';
				$response .= "\r\n" . '<li class="champ">Champion</li>';
				$response .= "\r\n" . '<div class="clear"></div>';
			$response .= "\r\n" . '</ul>';
		
			while ($row_getPastMenu = mysqli_fetch_array($result_getPastMenu)) {
				$response .= "\r\n" . '<ul class="season">';
					$response .= "\r\n" . '<li class="year">' . $row_getPastMenu['season'] . '</li>';
					$response .= "\r\n" . '<li class="rating"><a href="/past/' . $row_getPastMenu['season'] . '/ratings">Ratings</a></li>';
					$response .= "\r\n" . '<li class="sos"><a href="/past/' . $row_getPastMenu['season'] . '/sos">SOS</a></li>';
					$response .= "\r\n" . '<li class="champ">' . $row_getPastMenu['teamName'] . '</li>';
					$response .= "\r\n" . '<div class="clear"></div>';
				$response .= "\r\n" . '</ul>';
			}
			
		$response .= "\r\n" . '</ul>';
		
	}

	return $response;	
	
}

?>