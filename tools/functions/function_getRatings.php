<?php

function getRatings($type="ratings",$year=NULL) {
	
	if ($type=="sos") {
		$typeClause = "Sos";
		$finalHeaderSuffix = "Strength of Schedule";
		$headerPrefix = "Strength of Schedule ";
		$sortDirection = "ASC";
		$decimalPlaces = "5";
	} else {
		$typeClause = "Rating";
		$finalHeaderSuffix = "GBE Computer Ratings";
		$sortDirection = "DESC";
		$decimalPlaces = "1";
	}
		
	$query_getRatingsStatus = "SELECT * FROM ratingsIndex LIMIT 1";
	$result_getRatingsStatus = mysqli_query($GLOBALS['dbc'],$query_getRatingsStatus) or die ("Could not get ratings status: " . mysqli_error($GLOBALS['dbc']));
	if (mysqli_affected_rows($GLOBALS['dbc'])) {
		$row_getRatingsStatus = mysqli_fetch_array($result_getRatingsStatus);
	
		$seasonCurrent = $row_getRatingsStatus['seasonCurrent'];
		$seasonPrev = $seasonCurrent-1;
		$weekCurrent = $current_week = $row_getRatingsStatus['weekCurrent'];
			
		if ($year) {
			$seasonCurrent = $year;
			$weekCurrent = "Final";
		} elseif ($weekCurrent == "final") {
			$weekCurrent = "Final";
		}
		if ($year == "2003") {
			$whereClause = "AND seasonData.week" . $weekCurrent . $typeClause . " != '0'";
		}
		
		if ((!$year) && ($row_getRatingsStatus['weekCurrent'] == 'pre')) {
	
			$query_getSeasonPrevInfo = "SELECT seasonIndex.*,teamIndex.teamName,teamIndex.teamMascot FROM seasonIndex LEFT JOIN teamIndex ON seasonIndex.champId=teamIndex.id WHERE seasonIndex.season='$seasonPrev'";
			$result_getSeasonPrevInfo = mysqli_query($GLOBALS['dbc'],$query_getSeasonPrevInfo) or die ("Could not get previous season info: " . mysqli_error($GLOBALS['dbc']));
			if (mysqli_affected_rows($GLOBALS['dbc'])) {
				$row_getSeasonPrevInfo = mysqli_fetch_array($result_getSeasonPrevInfo);
			
				$champPrev = $row_getSeasonPrevInfo['teamName'];
				$champPrevMascot = $row_getSeasonPrevInfo['teamMascot'];
	
				$query_getSeasonInfo = "SELECT * FROM seasonIndex WHERE season='$seasonCurrent'";
				$result_getSeasonInfo = mysqli_query($GLOBALS['dbc'],$query_getSeasonInfo) or die ("Could not get current season info: " . mysqli_error($GLOBALS['dbc']));
				if (mysqli_affected_rows($GLOBALS['dbc'])) {
					$row_getSeasonInfo = mysqli_fetch_array($result_getSeasonInfo);
					
					$weekStamp = $row_getSeasonInfo['weekStart'];
					$weekName = date("F j",$weekStamp);
				}
				
			}

			$response .= "\r\n" . '<h2>Congratulations to the ' . $seasonPrev . ' GBE National Champion <span class="bold">' . $champPrev . ' ' . $champPrevMascot . '</span></h2>';
			$response .= "\r\n" . '<h2>The ' . $seasonCurrent . ' GBE College Football Ratings will be posted following the games of ' . $weekName . ', ' . $seasonCurrent . '</h2>';
			
		} else {
	
			$query_getSeasonStatus = "SELECT seasonIndex.*,teamIndex.teamName,teamIndex.teamMascot FROM seasonIndex LEFT JOIN teamIndex ON seasonIndex.champId=teamIndex.id WHERE seasonIndex.season='$seasonCurrent'";
			$result_getSeasonStatus = mysqli_query($GLOBALS['dbc'],$query_getSeasonStatus) or die ("Could not get current season info: " . mysqli_error($GLOBALS['dbc']));
			if (mysqli_affected_rows($GLOBALS['dbc'])) {
				$row_getSeasonStatus = mysqli_fetch_array($result_getSeasonStatus);
					
				if ($weekCurrent == "pre") {
					$weekStamp = $row_getSeasonStatus['weekStart'];
				} else {
					$weekStamp = (($weekCurrent-1)*604800)+$row_getSeasonStatus['weekStart'];
				}
				$weekName = date("F j",$weekStamp);

				if ($year) {
					
					$response .= "\r\n" . "<h1>" . $year . ' Final ' . $finalHeaderSuffix . "</h1>";
					
				} elseif ($weekCurrent == 'Final') {
		
					$champ = $row_getSeasonStatus['teamName'];
					$champMascot = $row_getSeasonStatus['teamMascot'];
					
					$response .= "\r\n" . '<h2>Congratulations to the ' . $seasonCurrent . ' GBE National Champion <span class="strong">' . $champ . ' ' . $champMascot . '</span></h2>';
					$response .= "\r\n" . '<h1>' . $seasonCurrent . ' Final Ratings</h1>';
				
				} else {
					
					$response .= "\r\n" . '<h1>' . $headerPrefix . 'Ratings as of games of ' . $weekName . ', ' . $seasonCurrent . '</h1>';
					
				}
		
//				$query_countTeams = "SELECT * FROM season" . $seasonCurrent . "";
				$query_countTeams = "SELECT * FROM seasonData WHERE season='$seasonCurrent'";
				$result_countTeams = mysqli_query($GLOBALS['dbc'],$query_countTeams) or die ("Could not get ratings: " . mysqli_error($GLOBALS['dbc']));
				if (mysqli_affected_rows($GLOBALS['dbc'])) {
					if ($seasonCurrent == "2003") {
						$teamsCount = "25";
					} else {
						$teamsCount = mysqli_affected_rows($GLOBALS['dbc']);
					}
					if (($teamsCount%2) == "0") {
						$teamsHalf = intval($teamsCount/2);
					} else {
						$teamsHalf = (intval($teamsCount/2)+1);
					}
					$teamsRemainder = $teamsCount - $teamsHalf;
					$rank = 1;
					
					$response .= "\r\n\t" . '<table id="ratingsBlockLeft" class="ratingsBlock">';
					
//						$query_getTopRatings = "SELECT season" . $seasonCurrent . ".*, teamIndex.teamName FROM season" . $seasonCurrent . " LEFT JOIN teamIndex ON season" . $seasonCurrent . ".teamId=teamIndex.id " . $whereClause . " ORDER BY week" . $weekCurrent . $typeClause . " $sortDirection, teamIndex.teamName ASC LIMIT 0,$teamsHalf";
						$query_getTopRatings = "SELECT seasonData.*, teamIndex.teamName FROM seasonData LEFT JOIN teamIndex ON seasonData.teamId=teamIndex.id WHERE 1=1 AND season='$seasonCurrent' " . $whereClause . " ORDER BY week" . $weekCurrent . $typeClause . " $sortDirection, teamIndex.teamName ASC LIMIT 0,$teamsHalf";
						$result_getTopRatings = mysqli_query($GLOBALS['dbc'],$query_getTopRatings) or die ("Could not get top half ratings: " . mysqli_error($GLOBALS['dbc']));
						if (mysqli_affected_rows($GLOBALS['dbc'])) {
							while ($row_getTopRatings = mysqli_fetch_array($result_getTopRatings)) {
	
								if ($type == "sos") {
									$multiplier = "1";
								} else {
									$multiplier = "10000";
								}
								
								$rating = ($row_getTopRatings['week' . $weekCurrent . $typeClause] * $multiplier);
									
								$team = $row_getTopRatings['teamName'];
								
								if ($rating != $ratingOld) {
									$rankDisplay = $rank;
								}

								$response .= "\r\n\t\t" . '<tr>';
									$response .= "\r\n\t\t" . '<td class="rank">' . $rankDisplay . '</td>';
									$response .= "\r\n\t\t" . '<td class="team">' . $team . '</td>';
									$response .= "\r\n\t\t" . '<td class="rating">' . number_format($rating,$decimalPlaces,'.','') . '</td>';
								$response .= "\r\n\t\t" . '</tr>';
								
								$ratingOld=$rating;
								$rank++;
							}
						}

					$response .= "\r\n\t" . '</table>';
					$response .= "\r\n\t" . '<table id="ratingsBlockRight" class="ratingsBlock">';
			
//						$query_getBottomRatings = "SELECT season" . $seasonCurrent . ".*, teamIndex.teamName FROM season" . $seasonCurrent . " LEFT JOIN teamIndex ON season" . $seasonCurrent . ".teamId=teamIndex.id WHERE 1=1 AND season='$seasonCurrent' " . $whereClause . " ORDER BY week" . $weekCurrent . $typeClause . " $sortDirection, teamIndex.teamName ASC LIMIT $teamsHalf,$teamsRemainder";
						$query_getBottomRatings = "SELECT seasonData.*, teamIndex.teamName FROM seasonData LEFT JOIN teamIndex ON seasonData.teamId=teamIndex.id WHERE 1=1 AND season='$seasonCurrent' " . $whereClause . " ORDER BY week" . $weekCurrent . $typeClause . " $sortDirection, teamIndex.teamName ASC LIMIT $teamsHalf,$teamsRemainder";
						$result_getBottomRatings = mysqli_query($GLOBALS['dbc'],$query_getBottomRatings) or die ("Could not get bottom half ratings: " . mysqli_error($GLOBALS['dbc']));
						if (mysqli_affected_rows($GLOBALS['dbc'])) {
							while ($row_getBottomRatings = mysqli_fetch_array($result_getBottomRatings)) {
	
								if ($type == "sos") {
									$multiplier = "1";
								} else {
									$multiplier = "10000";
								}
								
								$rating = ($row_getBottomRatings['week' . $weekCurrent . $typeClause] * $multiplier);
								
								$team = $row_getBottomRatings['teamName'];
								
								if ($rating != $ratingOld) {
									$rankDisplay = $rank;
								}

								$response .= "\r\n\t\t" . '<tr>';
									$response .= "\r\n\t\t" . '<td class="rank">' . $rankDisplay . '</td>';
									$response .= "\r\n\t\t" . '<td class="team">' . $team . '</td>';
									$response .= "\r\n\t\t" . '<td class="rating">' . number_format($rating,$decimalPlaces,'.','') . '</td>';
								$response .= "\r\n\t\t" . '</tr>';
								
								$ratingOld=$rating;
								$rank++;
							}
						}
					
					$response .= "\r\n\t" . '</table>';
					$response .= "\r\n\t" . '<div class="clear"></div>';
						
				}
			}
		}
	}
	
	return $response;
}

?>