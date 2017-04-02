<?php


//===============================================================================================//
// Task structure
//===============================================================================================//
class DarkTask
{
	public $id;
	public $title;
	public $project;
	public $description;
	public $status;
	public $startdate;
	public $enddate;
	public $delays;
	public $owner;

	//=========================================//
	// Construct
	function __construct ()
	{
		$this->id = -1;
		$this->title = "Title";
		$this->project = "AFTER";
		$this->description = "";
		$this->status = 0;
		$this->startdate = time();
		$this->enddate = time() + (7 * 24 * 60 * 60); // default to a week away
		$this->delays = 0;
		$this->owner = "";
	}
}


//===============================================================================================//
// Taskio
//===============================================================================================//
class DarkTasklistIO
{
	private function fixlist ( $tasklist )
	{
		$lastId = 0;
		// Check for invalid values in the tasklist and fix that
		foreach ( $tasklist AS $task )
		{
			// set ID
			if ( $task->id == -1 ) {
				$task->id = $lastId;
				$lastId += 1;
			}
			else if ( $task->id >= $lastId ) {
				$lastId = $task->id + 1;
			}
			// set project
			if ( !isset( $task->project ) ) {
				$task->project = "AFTER";
			}
		}
	}

	public function load ( $filename )
	{
		$tasklist = array();
        $t_filename = __DIR__ . "/../adata/" . $filename . ".json";

		if ( file_exists($t_filename) )
		{
			// Load up JSON code
			$json_value = file_get_contents($t_filename);
			$tasklist = json_decode($json_value);
		}
		$this->fixlist($tasklist);

		return $tasklist;
	}
	public function save ( $filename, $tasklist )
	{
		$this->fixlist($tasklist);

		// Save JSON
		{
			$json_value = json_encode($tasklist);
			file_put_contents( __DIR__ . "/../adata/" . $filename . ".json",$json_value);
		}
	}
}


?>
