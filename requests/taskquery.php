<?php

require_once __DIR__ . "/../components/main-content.php";
require_once __DIR__ . "/../components/tasklist.php";

if ( !array_key_exists("cmd",$_GET) ) {
	$_GET["cmd"] = "";
}
if ( !array_key_exists("cmd",$_POST) ) {
	$_POST["cmd"] = "";
}

switch ( $_GET["cmd"] )
{
	// change to panels
case "detail":
	{
		$taskId = $_GET["id"];
		$tasks = new DarkTasklist();
		$tasks->BuildDetails( $taskId );
	}
	break;
case "panel_newtask":
	{
		$tasks = new DarkTasklist();
		$tasks->BuildPanelNew();
	}
	break;
	// generate subpanels
case "panel_changestatus":
	{
		$taskId = $_GET["id"];
		$tasks = new DarkTasklist();
		$tasks->BuildPanelStatus( $taskId );
	}
	break;
case "panel_changedescription":
	{
		$taskId = $_GET["id"];
		$tasks = new DarkTasklist();
		$tasks->BuildPanelDescription( $taskId );
	}
	break;
case "panel_changeenddate":
	{
		$taskId = $_GET["id"];
		$tasks = new DarkTasklist();
		$tasks->BuildPanelDatepush( $taskId );
	}
	break;

default:
switch ( $_POST["cmd"] ) {
	case "addtask":
		{
			$tasks = new DarkTasklist();
			//$tasks->AddTask( $_POST["title"], $_POST["description"], $_POST["project"], $_POST["endtime"] );

            // create new task
            $task = new DarkTask();
            $task->title        = $_POST["title"];
            $task->project      = $_POST["project"];
            $task->description  = htmlspecialchars($_POST["description"]);
            $task->enddate      = $_POST["endtime"];
            $task->owner        = $_SERVER['REMOTE_USER'];

            // Add it to the list
            $tasks->AddTaskObject( $task );
		}
		break;
	case "status":
		{
			$tasks = new DarkTasklist();
			$tasks->TaskChangeStatus( $_POST["id"], $_POST["status"] );
		}
		break;
	case "description":
		{
			$tasks = new DarkTasklist();
			$tasks->TaskChangeDescription( $_POST["id"], $_POST["description"] );
		}
		break;
	case "enddate":
		{
			$tasks = new DarkTasklist();
			$tasks->TaskPushEnddate( $_POST["id"], $_POST["enddate"] );
		}
		break;
	}
	break;
}

?>
