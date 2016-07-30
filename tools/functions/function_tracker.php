<?php

function tracker() {
	
	$query_getRatingsStatus = "SELECT * FROM ratingsIndex LIMIT 1";
	$result_getRatingsStatus = mysqli_query($GLOBALS['dbc'],$query_getRatingsStatus) or die ("Could not get ratings status: " . mysql_error());
	if (mysqli_affected_rows($GLOBALS['dbc'])) {
		$row_getRatingsStatus = mysqli_fetch_array($result_getRatingsStatus);
	
		$seasonCurrent = $row_getRatingsStatus['seasonCurrent'];
		$seasonPrev = $seasonCurrent-1;
		$weekCurrent = $row_getRatingsStatus['weekCurrent'];
	
		$query_getSeasonStatus = "SELECT * FROM seasonIndex WHERE season='$seasonCurrent'";
		$result_getSeasonStatus = mysqli_query($GLOBALS['dbc'],$query_getSeasonStatus) or die ("Could not get season status: " . mysql_error());
		if (mysqli_affected_rows($GLOBALS['dbc'])) {
			$row_getSeasonStatus = mysqli_fetch_array($result_getSeasonStatus);
				
			if ($weekCurrent == "pre") {
				$weekStamp = $row_getSeasonStatus['weekStart'];
			} else {
				$weekStamp = (($weekCurrent-1)*604800)+$row_getSeasonStatus['weekStart'];
			}
			$weekName = date("F j",$weekStamp);
		
			if ($weekCurrent == 'pre') {
	
				$response .= "\r\n" . '<h1>The ' . $seasonCurrent . ' GBE College Football Ratings will be posted following the games of ' . $weekName . ', ' . $seasonCurrent . '</h1>';
			} else {

				$weekLastPlayed = $row_getSeasonStatus['lastWeekPlayed'];

				if ($weekCurrent == 'final') {
		
					$response .= "\r\n" . '<h1>' . $seasonCurrent . ' Final Ratings Tracker</h1>';
			
				} else {

					$response .= "\r\n" . '<h1>Weekly Ratings Tracker as of games of ' . $weekName . ', ' . $seasonCurrent . '</h1>';
		
				}
		
				$response .= "\r\n" . '<table id="tracker">';
			
					$response .= "\r\n" . '<tr>';
			
						$response .= "\r\n" . '<td class="team">&nbsp;</td>';
	
						for ($i=1;$i<=$weekLastPlayed;$i++) {
							$trackerDatestamp = (($i-1)*604800)+$row_getSeasonStatus['weekStart'];
							$trackerLabel = date("m/d",$trackerDatestamp);

							$response .= "\r\n" . '<th>' . $trackerLabel . '</th>';
						}
						if ($row_getSeasonStatus['finished'] == '1') {
							$response .= "\r\n" . '<th class="head">Final</th>';
						}

					$response .= "\r\n" . '</tr>';
	
//					$query_getRatings = "SELECT season" . $seasonCurrent . ".*, teamIndex.teamName FROM season" . $seasonCurrent . " LEFT JOIN teamIndex ON season" . $seasonCurrent . ".teamId=teamIndex.id ORDER BY week" . $weekCurrent . "Rating DESC, teamIndex.teamName ASC";
					$query_getRatings = "SELECT seasonData.*, teamIndex.teamName FROM seasonData LEFT JOIN teamIndex ON seasonData.teamId=teamIndex.id WHERE season='$seasonCurrent' ORDER BY week" . $weekCurrent . "Rating DESC, teamIndex.teamName ASC";
					$result_getRatings = mysqli_query($GLOBALS['dbc'],$query_getRatings) or die ("Could not get ratings: " . mysql_error());
					if (mysqli_affected_rows($GLOBALS['dbc'])) {
						while ($row_getRatings = mysqli_fetch_array($result_getRatings)) {
							
							$response .= '<tr>';
							
								$response .= "\r\n" . '<td class="team">' . $row_getRatings['teamName'] . '</td>';

								for ($i=1;$i<=$weekLastPlayed;$i++) {
									if ((!$teamHigh) || ($row_getRatings['week' . $i . 'Rank'] < $teamHigh)) {
										$teamHigh = $row_getRatings['week' . $i . 'Rank'];
									}
									if ((!$teamLow) || ($row_getRatings['week' . $i . 'Rank'] > $teamLow)) {
										$teamLow = $row_getRatings['week' . $i . 'Rank'];
									}
								}
								if ($row_getSeasonStatus['finished'] == '1') {
									if ((!$teamHigh) || ($row_getRatings['weekFinalRank'] < $teamHigh)) {
										$teamHigh = $row_getRatings['weekFinalRank'];
									}
									if ((!$teamLow) || ($row_getRatings['weekFinalRank'] > $teamLow)) {
										$teamLow = $row_getRatings['weekFinalRank'];
									}
								}
								for ($i=1;$i<=$weekLastPlayed;$i++) {
									if ($row_getRatings['week' . $i . 'Rank'] == $teamHigh) {
										$scoreClass = "teamHigh";
									} elseif ($row_getRatings['week' . $i . 'Rank'] == $teamLow) {
										$scoreClass = "teamLow";
									} else {
										unset($scoreClass);
									}
									$response .= "\r\n" . '<td class="rank ' . $scoreClass . '">' . $row_getRatings['week' . $i . 'Rank'] . '</td>';
								}
								if ($row_getSeasonStatus['finished'] == '1') {
									if ($row_getRatings['weekFinalRank'] == $teamHigh) {
										$scoreClass = "teamHigh";
									} elseif ($row_getRatings['weekFinalRank'] == $teamLow) {
										$scoreClass = "teamLow";
									} else {
										unset($scoreClass);
									}
									$response .= "\r\n" . '<td class="rank ' . $scoreClass . '">' . $row_getRatings['weekFinalRank'] . '</td>';
								}
								
								unset($teamHigh);
								unset($teamLow);
								
							$response .= "\r\n" . '</tr>';
						}
					}
					
				$response .= "\r\n" . '</table>';
				
			}
		}
	}

	return $response;
	
}

?>