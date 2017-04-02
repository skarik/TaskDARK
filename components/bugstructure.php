<?php

//===============================================================================================//
// Bug structure
//===============================================================================================//
class DarkBug
{
	public $title;
	public $project;
	public $description;
	public $status;
	public $history;
	public $id;

	//=========================================//
	// Construct
	function __construct ()
	{
		$this->title		= "Title";
		$this->project		= "AFTER";
		$this->description	= "";
		$this->status		= 0;
		$this->history		= Array();
		$this->id			= -1;
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
			if ( !isset( $bug->project ) ) {
				$bug->project = "AFTER";
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
