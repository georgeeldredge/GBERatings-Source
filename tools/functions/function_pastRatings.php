<?php

function pastRatings($year=NULL,$type=NULL) {
	
	require("function_getRatings.php");
	require("function_pastMenu.php");
	
	if (($type == "ratings") || ($type == "sos")) {
		
		$response .= getRatings($type,$year);
		
	} else {
		
		$response .= pastMenu();	
		
	}
	
	return $response;	
	
}

?>