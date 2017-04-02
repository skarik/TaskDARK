<?php

require_once __DIR__ . "/../components/main-content.php";
require_once __DIR__ . "/../components/idealist.php";

if ( !array_key_exists("cmd",$_GET) ) {
	$_GET["cmd"] = "";
}
if ( !array_key_exists("cmd",$_POST) ) {
	$_POST["cmd"] = "";
}

switch ( $_GET["cmd"] )
{
	// change to panels
case "detailidea":
	{
		$bugId = $_GET["id"];
		$buglist = new DarkIdealist();
		$buglist->BuildIdeaDetails( $bugId );
	}
	break;
case "showidea_add_panel":
	{
		$buglist = new DarkIdealist();
		$buglist->BuildNewideaPanel();
	}
	break;
	// subpanels
case "change_status_panel":
	{
		$bugId = $_GET["id"];
		$buglist = new DarkIdealist();
		$buglist->BuildChangeStatusPanel( $bugId );
	}
	break;
case "change_description_panel":
	{
		$bugId = $_GET["id"];
		$buglist = new DarkIdealist();
		$buglist->BuildChangeDescriptionPanel( $bugId );
	}
	break;
	
	// change bug status
case "change_idea_status":
	{
		$bugId = $_GET["id"];
		$bugStatus = $_GET["status"];
		$buglist = new DarkIdealist();
		$buglist->ChangeIdeaStatus( $bugId, $bugStatus );
	}

	
default:
switch ( $_POST["cmd"] ) {
	case "addidea":
		{
			$buglist = new DarkIdealist();
			$buglist->AddIdea( $_POST["title"], $_POST["description"], $_POST["project"] );
		}
		break;
	// change bug descriptiom
	case "change_idea_description":
		{
			$bugId = $_POST["id"];
			$bugDescription = $_POST["description"];
			$buglist = new DarkIdealist();
			$buglist->ChangeIdeaDescription( $bugId, $bugDescription );
		}
		break;
	}
}
?>