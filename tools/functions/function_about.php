<?php

function about() {
	
	$response .= "\r\n" . '<h1>About the GBE Computer Ratings</h1>';

	$response .= "\r\n" . '<p class="bold">Frequently Asked Questions</p>';
	
	$query_getFaq = "SELECT * FROM faq WHERE deleted='0' ORDER BY id ASC";
	$result_getFaq = mysqli_query($GLOBALS['dbc'],$query_getFaq) or die ("Could not get FAQ: " . mysql_error());
	if (mysqli_affected_rows($GLOBALS['dbc'])) {
		while ($row_getFaq = mysqli_fetch_array($result_getFaq)) {
			$questionList .= "\r\n" . '<li><a href="#' . $row_getFaq['id'] . '">' . $row_getFaq['question'] . '</a></li>';
			$answerList .= "\r\n" . '<li><a name="' . $row_getFaq['id'] . '"></a>' . $row_getFaq['question'] . '<br /><br />' . $row_getFaq['answer'] . '</li>';
		}
	}
	
	$response .= "\r\n" . '<ul>';
	$response .= $questionList;
	$response .= "\r\n" . '</ul>';
	
	$response .= "\r\n" . '<hr />';
	
	$response .= "\r\n" . '<ul>';
	$response .= $answerList;
	$response .= "\r\n" . '</ul>';
	
	return $response;
	
}

?>