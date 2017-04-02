<?php

require_once __DIR__ . "/../components/main-content.php";
require_once __DIR__ . "/../components/buglist.php";

if ( !array_key_exists("cmd",$_GET) ) {
	$_GET["cmd"] = "";
}
if ( !array_key_exists("cmd",$_POST) ) {
	$_POST["cmd"] = "";
}

switch ( $_GET["cmd"] )
{
	// change to panels
case "detailbug":
	{
		$bugId = $_GET["id"];
		$buglist = new DarkBuglist();
		$buglist->BuildBugDetails( $bugId );
	}
	break;
case "showbug_add_panel":
	{
		$buglist = new DarkBuglist();
		$buglist->BuildNewbugPanel();
	}
	break;
	// subpanels
case "change_status_panel":
	{
		$bugId = $_GET["id"];
		$buglist = new DarkBuglist();
		$buglist->BuildChangeStatusPanel( $bugId );
	}
	break;
case "change_description_panel":
	{
		$bugId = $_GET["id"];
		$buglist = new DarkBuglist();
		$buglist->BuildChangeDescriptionPanel( $bugId );
	}
	break;
	
	// change bug status
case "change_bug_status":
	{
		$bugId = $_GET["id"];
		$bugStatus = $_GET["status"];
		$buglist = new DarkBuglist();
		$buglist->ChangeBugStatus( $bugId, $bugStatus );
	}

	
default:
switch ( $_POST["cmd"] ) {
	case "addbug":
		{
			$buglist = new DarkBuglist();
			$buglist->AddBug( $_POST["title"], $_POST["description"], $_POST["project"] );
		}
		break;
	// change bug descriptiom
	case "change_bug_description":
		{
			$bugId = $_POST["id"];
			$bugDescription = $_POST["description"];
			$buglist = new DarkBuglist();
			$buglist->ChangeBugDescription( $bugId, $bugDescription );
		}
		break;
	}
}

?>