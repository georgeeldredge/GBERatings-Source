<?php

$query_getSiteSettings = "SELECT * FROM siteSettings";
$result_getSiteSettings = mysqli_query($dbc,$query_getSiteSettings) or die ("Could not get site settings: " . mysql_error());
if (mysqli_affected_rows($dbc)) {
	while ($row_getSiteSettings = mysqli_fetch_array($result_getSiteSettings)) {
		${$row_getSiteSettings['settingName']} = $row_getSiteSettings['settingValue'];
	}
}

$semanticReplacements['staff'] = array (
								"&eacute;"	=>	"e",
								","			=>	"",
								"\""		=>	"",
								"\'"		=>	"",
								"$"			=>	"",
								"&"			=>	"and",
								"."			=>	"",
);

?>