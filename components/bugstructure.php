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

		/*else if ( file_exists(__DIR__ . "/" . $filename . ".xml") )
		{
			$xml = new DOMDocument();
			$xml->load( __DIR__ . "/" . $filename  . ".xml");

			$x = $xml->documentElement;
			foreach ( $x->childNodes AS $item )
			{
				$bug = new DarkBug();
				$bug->title = $item->getAttribute( "title" );
				$bug->status = $item->getAttribute( "status" );
				foreach ( $item->childNodes AS $props )
				{
					if ( $props->nodeName == "description" ) {
						$bug->description = $props->nodeValue;
					}
					else if ( $props->nodeName == "history" ) {
						foreach ( $props->childNodes AS $pasts )
						{
							array_push( $bug->history, $pasts->nodeValue );
						}
					}
				}
				if ( $item->hasAttribute( "id" ) ) {
					$bug->id = $item->getAttribute( "id" );
				}
				else {
					$bug->id = $lastId;
					$lastId += 1;
				}
				array_push( $buglist, $bug );
			}
		}*/

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

		// Save XML
		/*{
			$xml = new DOMDocument();
			$root = $xml->createElement( "data" );
			$xml->appendChild( $root );

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

				// create item
				$xml_bug = $xml->createElement( "bug" );
				$xml_bug->setAttribute( "title", $bug->title );
				$xml_bug->setAttribute( "status", $bug->status );
				$xml_bug->setAttribute( "id", $bug->id );
				$xml_bug->appendChild( $xml->createElement( "description", $bug->description ) );
				// create bug list
				$xml_bug_history = $xml->createElement( "history" );
				foreach ( $bug->history AS $bughistory ) {
					$xml_bug_history->appendChild( $xml->createElement( "info", $bughistory ) );
				}
				$xml_bug->appendChild( $xml_bug_history );

				// add item to the xml doc
				$root->appendChild( $xml_bug );
			}

			$xml->save( __DIR__ . "/" . $filename . ".xml");
		}*/

	}
}

?>
