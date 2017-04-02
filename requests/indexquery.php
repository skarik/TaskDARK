<?php

require_once __DIR__ . "/../components/main-content.php";

$contentCreator = new DarkMainContent();

if ( $_GET["page"] == "calendar" ) {
	$contentCreator->EmitCalendar();
}
else if ( $_GET["page"] == "tasks" ) {
	$contentCreator->EmitTasklist();
}
else if ( $_GET["page"] == "bugs" ) {
	$contentCreator->EmitBuglist(intval($_GET["type"]));
}
else if ( $_GET["page"] == "ideas" ) {
	$contentCreator->EmitIdealist(intval($_GET["type"]));
}
else if ( $_GET["page"] == "mainpage" ) {
	$contentCreator->EmitMainpage();
}

?>