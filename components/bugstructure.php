<?php

//===============================================================================================//
// Bug structure
//===============================================================================================//
class DarkBug
{
    public $id;

	public $title;
	public $project;
	public $description;
	public $status;

    public $severity;       // How severe does this bug impact shit?
    public $priority;       // How important is it to fix this bug?
    public $entertaining;   // Is this bug entertaining to reccreate?
    public $reporter;       // User who created the bug
    public $assignee;       // User who has started the terrible task to fix the bug

    public $history;

	//=========================================//
	// Construct
	function __construct ()
	{
        $this->id			= -1;

		$this->title		= "Title";
		$this->project		= "AFTER";
		$this->description	= "";
		$this->status		= 0;

        $this->severity     = 0;
        $this->priority     = 1;
        $this->entertaining = false;
        $this->reporter     = "";
        $this->assignee     = "";

        $this->history		= Array();
	}
}

//===============================================================================================//
// Bugio
//===============================================================================================//
class DarkBuglistIO
{
	private function fixlist ( $buglist )
	{
		$lastId = 0;
		// Check for invalid values in the buglist and fix that
		foreach ( $buglist AS $bug )
		{
			// set ID
			if ( $bug->id == -1 ) {
				$bug->id = $lastId;
				$lastId += 1;
			}
			else if ( $bug->id >= $lastId ) {
				$lastId = $bug->id + 1;
			}
            // set project
			if ( !isset( $bug->project ) ) {
				$bug->project = "AFTER";
			}
            // set severity
            if ( !isset( $bug->severity ) ) {
                $bug->severity = 0;
            }
            // set priority
            if ( !isset( $bug->priority ) ) {
                $bug->priority = 1;
            }
            // set entertaining
            if ( !isset( $bug->entertaining ) ) {
                $bug->entertaining = false;
            }
            // set reporter
            if ( !isset( $bug->reporter ) ) {
                $bug->reporter = "Unknown";
            }
            // set assignee
            if ( !isset( $bug->assignee ) ) {
                $bug->assignee = "";
            }
		}
	}

	public function load ( $filename )
	{
		$lastId = 0;
		$buglist = array();

        $t_filename = __DIR__ . "/../adata/" . $filename . ".json";
		if ( file_exists($t_filename) )
		{
			// Load up JSON code
			$json_value = file_get_contents($t_filename);
			$buglist = json_decode($json_value);
		}
		$this->fixlist($buglist);

		return $buglist;
	}
	public function save ( $filename, $buglist )
	{
		$this->fixlist($buglist);
		// Save JSON
		{
			$json_value = json_encode($buglist);
			file_put_contents( __DIR__ . "/../adata/" . $filename . ".json",$json_value);
		}
	}
}

?>
