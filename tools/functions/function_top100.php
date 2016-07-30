<?php

function top100() {
	
	$response .= "\r\n" . '<h1>Top 100 Strongest Teams Computed in the GBE Computer Ratings<br />
		2003-present</h1>';
		
	$response .= "\r\n" . '<table id="ratingsBlockLeft" class="ratingsBlock">';

		$rank = 1;

		$query_getTop50 = "SELECT seasonData.*, teamIndex.teamName FROM seasonData LEFT JOIN teamIndex ON seasonData.teamId=teamIndex.id ORDER BY weekFinalRating DESC, teamName ASC LIMIT 0,50";
		$result_getTop50 = mysqli_query($GLOBALS['dbc'],$query_getTop50) or die ("Could not get 1-50: " . mysql_error());
		if (mysqli_affected_rows($GLOBALS['dbc'])) {
			while ($row_getTop50 = mysqli_fetch_array($result_getTop50)) {

				$rating = ($row_getTop50['weekFinalRating'] * 10000);
				if ($rating != $ratingOld) {
					$rankDisplay = $rank;
				}
				
				$response .= "\r\n" . '<tr>';

					$response .= "\r\n" . '<td class="rank">' . $rankDisplay . '</td>';
					$response .= "\r\n" . '<td class="team">' . $row_getTop50['teamName'] . ' (' . $row_getTop50['season'] . ')</td>';
					$response .= "\r\n" . '<td class="rating">' . number_format($rating,1,'.','') . '</td>';

				$response .= "\r\n" . '</tr>';

				$ratingOld = $rating;
				$rank++;
				
			}
		}
		
		$response .= "\r\n" . '</table>';
		$response .= "\r\n" . '<table id="ratingsBlockRight" class="ratingsBlock">';
		
		$query_getNext50 = "SELECT seasonData.*, teamIndex.teamName FROM seasonData LEFT JOIN teamIndex ON seasonData.teamId=teamIndex.id ORDER BY weekFinalRating DESC, teamName ASC LIMIT 50,50";
		$result_getNext50 = mysqli_query($GLOBALS['dbc'],$query_getNext50) or die ("Could not get 21-40: " . mysql_error());
		if (mysqli_affected_rows($GLOBALS['dbc'])) {
			while ($row_getNext50 = mysqli_fetch_array($result_getNext50)) {

				$rating = ($row_getNext50['weekFinalRating'] * 10000);
				if ($rating != $ratingOld) {
					$rankDisplay = $rank;
				}

				$response .= "\r\n" . '<tr>';
					$response .= "\r\n" . '<td class="rank">' . $rankDisplay . '</td>';
					$response .= "\r\n" . '<td class="team">' . $row_getNext50['teamName'] . ' (' . $row_getNext50['season'] . ')</td>';
					$response .= "\r\n" . '<td class="rating">' . number_format($rating,1,'.','') . '</td>';
				$response .= "\r\n" . '</tr>';

				$ratingOld = $rating;
				$rank++;
				
			}
		}

		$response .= "\r\n" . '</table>';
		$response .= "\r\n" . '<div class="clear"></div>';
	$response .= "\r\n" . '</ul>';

	return $response;	
	
}

?>