<?php
if ($_SESSION['userId']) {
?>
	<div id="pageEdit">
<?php
		if (strstr($sourcePermissions,"G" . $_SESSION['groupId'] . "G")) {
?>
			<a href="/cms/index.php?m=content&amp;pageId=<?=$pageId?>" target="_blank">Edit This Page</a>
<?php
		}
?>
	</div>
<?php
}
?>