<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>TaskDARK</title>
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<div id="main-content">
<?php

require_once "components/calendar.php";
require_once "components/buglist.php";
require_once "components/tasklist.php";
require_once "components/mainpage.php";
require_once "components/main-content.php";

//$calendar = new DarkCalendar();
//$calendar->Build();
//$buglist = new DarkBuglist();
//$buglist->Build();
$main = new DarkMainContent();
$main->EmitMainpage();

?>
		</div>
	<script type="text/javascript" src="requests/queries.js"></script>
	</body>
</html>