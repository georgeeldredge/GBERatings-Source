<?php

function conferenceRatings($conf=NULL) {
	
	$query_getRatingsStatus = "SELECT * FROM ratingsIndex LIMIT 1";
	$result_getRatingsStatus = mysqli_query($GLOBALS['dbc'],$query_getRatingsStatus) or die ("Could not get ratings status: " . mysql_error());
	if (mysqli_affected_rows($GLOBALS['dbc'])) {
		$row_getRatingsStatus = mysqli_fetch_array($result_getRatingsStatus);

		$weekCurrent = $row_getRatingsStatus['weekCurrent'];
		$seasonCurrent = $row_getRatingsStatus['seasonCurrent'];
		$seasonPrev = $seasonCurrent-1;
		
		if ($weekCurrent == "final") {
			$weekCurrent = "Final";
		}

		$query_getSeasonStatus = "SELECT * FROM seasonIndex WHERE season='$seasonCurrent'";
		$result_getSeasonStatus = mysqli_query($GLOBALS['dbc'],$query_getSeasonStatus) or die ("Could not get season status: " . mysql_error());;
		if (mysqli_affected_rows($GLOBALS['dbc'])) {
			$row_getSeasonStatus = mysqli_fetch_array($result_getSeasonStatus);
					
			if ($weekCurrent == "pre") {
				$weekStamp = $row_getSeasonStatus['weekStart'];
			} else {
				$weekStamp = (($weekCurrent-1)*604800)+$row_getSeasonStatus['weekStart'];
			}
			$weekName = date("F j",$weekStamp);
			
			if ($conf) {

				$query_getConfName = "SELECT id, confName FROM confIndex WHERE confAbbv='$conf' LIMIT 1";
				$result_getConfName = mysqli_query($GLOBALS['dbc'],$query_getConfName) or die ("Could not get conference name: " . mysql_error());
				if (mysqli_affected_rows($GLOBALS['dbc'])) {
					$row_getConfName = mysqli_fetch_array($result_getConfName);
					$confId = $row_getConfName['id'];
					$confName = $row_getConfName['confName'];
				} else {
					header("HTTP/1.1 404 Not Found");
					header("Location: /conf");				
				}
				
			}

			if (($conf) && ($weekCurrent != "pre")) {

				$rank = '1';

				if ($weekCurrent == "Final") {
					$response .= "\r\n" . '<h1>Final ' . $confName . ' ' . $seasonCurrent . ' Ratings</h1>';
				} else {
					$response .= "\r\n" . '<h1>' . $confName . ' Ratings as of games of ' . $weekName . ', ' . $seasonCurrent . '</h1>';
				}

				$response .= "\r\n" . '<table class="ratingsBlock">';

//					$query_getConfRatings = "SELECT season" . $seasonCurrent . ".*,teamIndex.teamName FROM season" . $seasonCurrent . " LEFT JOIN teamIndex ON season" . $seasonCurrent . ".teamId=teamIndex.id WHERE season" . $seasonCurrent . ".confId='$confId' ORDER BY week" . $weekCurrent . "Rating DESC, teamId ASC";
					$query_getConfRatings = "SELECT seasonData.*,teamIndex.teamName FROM seasonData LEFT JOIN teamIndex ON seasonData.teamId=teamIndex.id WHERE season='$seasonCurrent' AND seasonData.confId='$confId' ORDER BY week" . $weekCurrent . "Rating DESC, teamId ASC";
					$result_getConfRatings = mysqli_query($GLOBALS['dbc'],$query_getConfRatings) or die ("Could not get conference ratings: " . mysql_error());
					if (mysqli_affected_rows($GLOBALS['dbc'])) {
						while ($row_getConfRatings = mysqli_fetch_array($result_getConfRatings)) {

							$rating = ($row_getConfRatings['week' . $weekCurrent . 'Rating'] * 10000);
							if ($rating != $ratingOld) {
								$rankDisplay = $rank;
							}
							
							$response .= "\r\n" . '<tr>';
					
								$response .= "\r\n" . '<td class="rank">' . $rankDisplay . '</td>';
								$response .= "\r\n" . '<td class="team">' . $row_getConfRatings['teamName'] . '</td>';
								$response .= "\r\n" . '<td class="rating">' . number_format($rating,1,'.','') . '</td>';
								
							$response .= "\r\n" . '</tr>';
					
							$ratingOld = $rating;
							$rank++;
						}
					}

				$response .= "\r\n" . '</table>';

				$response .= "\r\n" . '<h2><a href="/conf">Return to conference ratings index</a></h2>';

			} elseif ($weekCurrent == 'pre') {

				$response .= "\r\n" . '<h2>The ' . $seasonCurrent . ' GBE College Football Ratings will be posted following the games of ' . $weekName . ', ' . $seasonCurrent . '</h2>';

			} else {

				if ($weekCurrent == 'Final') {

					$response .= "\r\n" . '<h1>' . $seasonCurrent . ' Final Conference Ratings</h1>';

				} else {

					$response .= "\r\n" . '<h1>Conference Ratings as of games of ' . $weekName . ', ' . $seasonCurrent . '</h1>';

				}

				$rank = 1;
				
				$query_getConferences = "SELECT id,confName,confAbbv FROM confIndex ORDER BY id";
				$result_getConferences = mysqli_query($GLOBALS['dbc'],$query_getConferences) or die ("Could not get conferences: " . mysql_error());
				if (mysqli_affected_rows($GLOBALS['dbc'])) {
					while ($row_getConferences = mysqli_fetch_array($result_getConferences)) {
					
						$confRating = 0;
						$confTeams = 0;
						
						$confId = $row_getConferences['id'];
						$confName[$confId] = $row_getConferences['confName'];
						$confAbbv[$confId] = $row_getConferences['confAbbv'];

						$query_getConfTeams = "SELECT id,currentRating FROM teamIndex WHERE confId='$confId'";
						$result_getConfTeams = mysqli_query($GLOBALS['dbc'],$query_getConfTeams) or die ("Could not get conference teams: " . mysql_error());
						if (mysqli_affected_rows($GLOBALS['dbc'])) {
							while ($row_getConfTeams = mysqli_fetch_array($result_getConfTeams)) {
								$confTeams++;
								$confRating += $row_getConfTeams['currentRating'];
							}
						}

						$confRatingAverage[$confId] = ($confRating / $confTeams);
					
					}

				}
				
				arsort($confRatingAverage);

				$response .= "\r\n" . '<table class="ratingsBlock">';
					foreach ($confRatingAverage as $confId => $confRating) {
						$rating = ($confRating * 10000);
						if ($rating != $ratingOld) {
							$rankDisplay = $rank;
						}

						$response .= "\r\n" . '<tr>';
		
							$response .= "\r\n" . '<td class="rank">' . $rankDisplay . '</td>';
							$response .= "\r\n" . '<td class="team"><a href="/conf/' . $confAbbv[$confId] . '">' . $confName[$confId] . '</a></td>';
							$response .= "\r\n" . '<td class="rating">' . number_format($rating,1,'.','') . '</td>';
					
						$response .= "\r\n" . '</tr>';
	
						$ratingOld = $rating;
						$rank++;
					}
			
				$response .= "\r\n" . '</table>';
				
				$response .= "\r\n" . '<h2>Click on a conference name to show that conference\'s member school ratings</h2>';

			}
		}
	}
            
	return $response;
	
}

?>