<?php

function top40() {
	
	$response .= "\r\n" . '<h1>Top 40 Strongest Teams Computed in the GBE Computer Ratings<br />
		2003-present</h1>';
		
	$response .= "\r\n" . '<table id="ratingsBlockLeft" class="ratingsBlock">';

		$rank = 1;

//		$query_getTop20 = "SELECT top40.*, teamIndex.teamName FROM top40 LEFT JOIN teamIndex ON top40.teamId=teamIndex.id ORDER BY score DESC, teamName ASC LIMIT 0,20";
		$query_getTop20 = "SELECT seasonData.*, teamIndex.teamName FROM seasonData LEFT JOIN teamIndex ON seasonData.teamId=teamIndex.id ORDER BY weekFinalRating DESC, teamName ASC LIMIT 0,20";
		$result_getTop20 = mysqli_query($GLOBALS['dbc'],$query_getTop20) or die ("Could not get 1-20: " . mysql_error());
		if (mysqli_affected_rows($GLOBALS['dbc'])) {
			while ($row_getTop20 = mysqli_fetch_array($result_getTop20)) {

//				$rating = ($row_getTop20['score'] * 10000);
				$rating = ($row_getTop20['weekFinalRating'] * 10000);
				if ($rating != $ratingOld) {
					$rankDisplay = $rank;
				}
				
				$response .= "\r\n" . '<tr>';

					$response .= "\r\n" . '<td class="rank">' . $rankDisplay . '</td>';
//					$response .= "\r\n" . '<td class="team">' . $row_getTop20['teamName'] . ' (' . $row_getTop20['year'] . ')</td>';
					$response .= "\r\n" . '<td class="team">' . $row_getTop20['teamName'] . ' (' . $row_getTop20['season'] . ')</td>';
					$response .= "\r\n" . '<td class="rating">' . number_format($rating,1,'.','') . '</td>';

				$response .= "\r\n" . '</tr>';

				$ratingOld = $rating;
				$rank++;
				
			}
		}
		
		$response .= "\r\n" . '</table>';
		$response .= "\r\n" . '<table id="ratingsBlockRight" class="ratingsBlock">';
		
//		$query_getNext20 = "SELECT top40.*, teamIndex.teamName FROM top40 LEFT JOIN teamIndex ON top40.teamId=teamIndex.id ORDER BY score DESC, teamName ASC LIMIT 20,20";
		$query_getNext20 = "SELECT seasonData.*, teamIndex.teamName FROM seasonData LEFT JOIN teamIndex ON seasonData.teamId=teamIndex.id ORDER BY weekFinalRating DESC, teamName ASC LIMIT 20,20";
		$result_getNext20 = mysqli_query($GLOBALS['dbc'],$query_getNext20) or die ("Could not get 21-40: " . mysql_error());
		if (mysqli_affected_rows($GLOBALS['dbc'])) {
			while ($row_getNext20 = mysqli_fetch_array($result_getNext20)) {

//				$rating = ($row_getNext20['score'] * 10000);
				$rating = ($row_getNext20['weekFinalRating'] * 10000);
				if ($rating != $ratingOld) {
					$rankDisplay = $rank;
				}

				$response .= "\r\n" . '<tr>';
					$response .= "\r\n" . '<td class="rank">' . $rankDisplay . '</td>';
//					$response .= "\r\n" . '<td class="team">' . $row_getNext20['teamName'] . ' (' . $row_getNext20['year'] . ')</td>';
					$response .= "\r\n" . '<td class="team">' . $row_getNext20['teamName'] . ' (' . $row_getNext20['season'] . ')</td>';
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