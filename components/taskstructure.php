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
		/*$xml = new DOMDocument();
		$xml->load( __DIR__ . "/" . $filename . ".xml" );

		$x = $xml->documentElement;
		foreach ( $x->childNodes AS $item )
		{
			$task = new DarkTask();
			$task->id = $item->getAttribute( "id" );
			$task->title = $item->getAttribute( "title" );
			$task->status = $item->getAttribute( "status" );
			foreach ( $item->childNodes AS $props )
			{
				if ( $props->nodeName == "description" ) {
					$task->description = $props->nodeValue;
				}
				else if ( $props->nodeName == "startdate" ) {
					$task->startdate = intval($props->nodeValue);
				}
				else if ( $props->nodeName == "enddate" ) {
					$task->enddate = intval($props->nodeValue);
				}
				else if ( $props->nodeName == "owner" ) {
					$task->owner = $props->nodeValue;
				}
			}
			$task->delays = $item->getAttribute( "delays" );
			array_push( $tasklist, $task );
		}*/
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

		// Save XML
		/*{
			$lastId = 0;
			$xml = new DOMDocument();

			$root = $xml->createElement( "data" );
			$xml->appendChild( $root );

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

				// create item
				$xml_task = $xml->createElement( "task" );
				$xml_task->setAttribute( "id", $task->id );
				$xml_task->setAttribute( "title", $task->title );
				$xml_task->setAttribute( "status", $task->status );
				$xml_task->setAttribute( "delays", intval($task->delays) );
				$xml_task->appendChild( $xml->createElement( "description", $task->description ) );
				$xml_task->appendChild( $xml->createElement( "startdate", intval($task->startdate) ) );
				$xml_task->appendChild( $xml->createElement( "enddate", intval($task->enddate) ) );
				$xml_task->appendChild( $xml->createElement( "owner", $task->owner ) );

				// add item to the xml doc
				$root->appendChild( $xml_task );
			}

			$xml->save( __DIR__ . "/" . $filename );
		}*/
	}
}


?>
