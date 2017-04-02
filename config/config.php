<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$GLOBALS["TASKDARK_PROJECTS"] = array(
	"AFTER"		=> "(AFTER) AFTER",
    "M02"		=> "(M02) VN",
	"M04-BASE"	=> "(M04-BASE) M04 Engine",
	"M04-ENPA"	=> "(M04-ENPA) M04_Endoparasite",
    "M04-LUVP"	=> "(M04-LUVP) M04 Love People",
	"TASKDARK"	=> "(TASKDARK) TaskDARK"
);

$GLOBALS["TASKDARK_PROPOSAL_STATUS"] = array(
	0	=> "Awaiting review",
	1	=> "Rejected",
	2	=> "Accepted",
	3	=> "Implemented"
);

function IsAdmin ( $project )
{
	if ( $_SERVER['REMOTE_USER'] == "skarik" )
	{
		return true;
	}
	return false;
}


?>
