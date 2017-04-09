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
case "change_priority_panel":
	{
		$bugId = $_GET["id"];
		$buglist = new DarkBuglist();
		$buglist->BuildChangePriorityPanel( $bugId );
	}
	break;
case "change_severity_panel":
	{
		$bugId = $_GET["id"];
		$buglist = new DarkBuglist();
		$buglist->BuildChangeSeverityPanel( $bugId );
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
    break;
case "change_bug_priority":
    {
		$bugId = $_GET["id"];
		$buglist = new DarkBuglist();

		$bug = $buglist->GetBug( $bugId );

        $bug->priority = $_GET["priority"];
        $newhistory = array(
            "date" => date("Y-m-d H:i:s"),
            "info" => "Priority changed to " . $bug->priority . " by " . $_SERVER['REMOTE_USER'],
            "detail" => $bug->priority,
        );
        array_push( $bug->history, $newhistory );

        $buglist->SetBug( $bugId, $bug );
        $buglist->BuildBugDetails( $bugId );
	}
    break;
case "change_bug_severity":
    {
		$bugId = $_GET["id"];
		$buglist = new DarkBuglist();

		$bug = $buglist->GetBug( $bugId );

        $bug->severity = $_GET["severity"];
        $newhistory = array(
            "date" => date("Y-m-d H:i:s"),
            "info" => "Severity changed to " . $bug->severity . " by " . $_SERVER['REMOTE_USER'],
            "detail" => $bug->severity,
        );
        array_push( $bug->history, $newhistory );

        $buglist->SetBug( $bugId, $bug );
        $buglist->BuildBugDetails( $bugId );
	}
    break;

default:
switch ( $_POST["cmd"] ) {
	case "addbug":
		{
			$buglist = new DarkBuglist();
			//$buglist->AddBug( $_POST["title"], $_POST["description"], $_POST["project"] );
            $bug = new DarkBug();
            $bug->title     = $_POST["title"];
            $bug->project   = $_POST["project"];
            $bug->description   = htmlspecialchars($_POST["description"]);

            $bug->severity  = intval($_POST["severity"]);
            $bug->priority  = intval($_POST["priority"]);
            $bug->entertaining  = $_POST["entertaining"] === 'true';
            $bug->reporter  = $_SERVER['REMOTE_USER'];
            $bug->assignee  =  $_POST["assignee"];

            $buglist->AddBugObject($bug);
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
