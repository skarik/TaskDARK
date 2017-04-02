<?php
require_once __DIR__ . "/../config/config.php"; // Include taskdark config
require_once __DIR__ . "/bugstructure.php";		// Include bug info


class DarkBuglist
{
	public $buglist;
	
	public function Debug()
	{
		/*echo( "Swagtastrophy" );*/
		//unset($buglist);
		//$buglist = array();
		/*$bug = new DarkBug();
		$bug->title = "Problems in life";
		$bug->description = "is it really too much?";
		array_push( $buglist, $bug );*/
		//$io->save("buglist.xml",$buglist);
	}

	//=========================================//
	// Output buglist
	public function Build ( $type = 0 )
	{
		// Load up the bug list
		$io = new DarkBuglistIO();
		$this->buglist = $io->load("buglist");
		$io->save("buglist", $this->buglist);
		
		echo( "<div class=\"page-title\">Buglist <sub>Track issues</sub></div>" );
		
		$this->BuildMenu();
		$this->BuildListing($type);
	}
	
	//=========================================//
	// Output bug details
	public function BuildBugDetails ( $id )
	{
		// Load up the bug list
		$io = new DarkBuglistIO();
		$this->buglist = $io->load("buglist");
		
		foreach ( $this->buglist AS $bug )
		{
			if ( $bug->id == $id )
			{
				$statusString = "Unresolved";
				if ( $bug->status == 1 ) {
					$statusString = "Unknown";
				}
				else if ( $bug->status == 2 ) {
					$statusString = "Fixed";
				}
				$historyString = "";
				foreach ( $bug->history AS $pasts )
				{
					$historyString = $historyString .
						'<div class="detail-history-row">' .
							'<span class="detail-history-date">' . $pasts->date . '</span>' .
							'<span class="detail-history-info">' . $pasts->info . '</span>' .
						'</div>';
				}
				if ( $historyString == "" ) {
					$historyString = "No history";
				}
				echo(
					'<div class="detail-container">' .
						'<div class="detail-heading" id="info-title">' .
							$bug->title .
						'</div>' .
						'<div class="detail-info" id="info-description">' .
							nl2br($bug->description) .
							'<a href="" onclick="return bugChangeDescription('.$id.')">' .
							'<div class="detail-button">' .
								"(change)" .
							'</div>' .
							'</a>' .
						'</div>' .
						'<div class="detail-heading">' .
							"STATUS" .
						'</div>' .
						'<div class="detail-info" id="info-status">' .
							$statusString .
							'<a href="" onclick="return bugChangeStatus('.$id.')">' .
							'<div class="detail-button">' .
								"(change)" .
							'</div>' .
							'</a>' .
						'</div>' .
						'<div class="detail-heading">' .
							"HISTORY" .
						'</div>' .
						'<div class="detail-info">' .
							$historyString .
						'</div>' .
					'</div>'
					);
				// found bug, break out of loop
				break;
			}
		}
	}
	
	//=========================================//
	// Output a new bug panel
	public function BuildNewbugPanel ()
	{
		echo( "<div class=\"input-container\">" );
		echo( 
				'<div class="input-box-heading">Title</div>' .
				'<textarea class="input-box-oneline" id="input-title" rows="1" cols="200" name="input-title"></textarea>' .
				'<div class="input-box-heading">Project</div>' .
				'<select class="input-box-dropdown" id="input-project">'
				);
		foreach ( $GLOBALS["TASKDARK_PROJECTS"] as $pid => $name )
		{
			echo( '<option value="' . $pid . '">' . $name . '</option>' );
		}
		echo(
				'</select>' .
				'<div class="input-box-heading">Description</div>' .
				'<textarea class="input-box-multiline" id="input-description" rows="10" cols="200" name="input-description"></textarea>' .
				'<br>' .
				'<a href="" onclick="return bugNewBug();">' .
					'<div class="input-box-submit">Submit</div>' .
				'</a>' .
				'<a href="" onclick="return showBugs();">' .
					'<div class="input-box-submit">Discard and Cancel</div>' .
				'</a>' 
			);
		echo( "</div>" );
	}
	
	//=========================================//
	// Output status change panel
	public function BuildChangeStatusPanel ( $id )
	{	
		echo( "<div>This change will be recorded.</div>" );
		// print 3 buttons, each referring to the proper
		$bug = $this->GetBug( $id );
		$statusTypes = array( 'UNRESOLVED', 'UNKNOWN', 'FIXED' );
		for ( $i = 0; $i < 3; $i += 1 )
		{
			echo( '<a href="" onclick="return bugSendChangeStatus('.$id.','.$i.');">' );
			if ( $bug->status == $i ) {
				echo( '<span class="menu-choice-current">' );
			}
			else {
				echo( '<span class="menu-choice">' );
			}
			echo( $statusTypes[$i] . "</span></a>" );
		}
	}
	
	//=========================================//
	// Output description change panel
	public function BuildChangeDescriptionPanel ( $id )
	{
		// Load up the bug list
		$io = new DarkBuglistIO();
		$this->buglist = $io->load("buglist");
		
		$descriptionString = "";
		foreach ( $this->buglist AS $bug )
		{
			if ( $bug->id == $id )
			{
				$descriptionString = $bug->description;
				break;
			}
		}
		
		// print the fuckin text box
		echo(
			'<textarea class="input-box-multiline" id="input-description" rows="10" cols="200" name="input-description">' .
				$descriptionString .
			'</textarea>'
			);
		
		// print confirm button
		echo( 
			'<div>This change will be recorded.</div>' .
			'<a href="#" onclick="return bugSendChangeDescription('.$id.');">' .
				'<span class="menu-choice">Submit Change</span>' .
			'</a>'
			);
	}
	
	//===============================================================================================//
	// List editing
	//===============================================================================================//

	//=========================================//
	// Adds a new bug to the list. Fucking crazy.
	public function AddBug ( $title, $description, $project )
	{
		// Load up the bug list
		$io = new DarkBuglistIO();
		$this->buglist = $io->load("buglist");
		
		echo( $title . "::::" . $description );
		
		// create new bug
		$bug = new DarkBug();
		$bug->title = $title;
		$bug->description = htmlspecialchars($description);
		$bug->project = $project;
		$newhistory = array(
			"date" => date("Y-m-d H:i:s"),
			"info" => "Bug created by " . $_SERVER['REMOTE_USER'],
			"detail" => "",
		);
		// Add it to the history
		array_push( $bug->history, $newhistory );
		// push it to the list kekekekek
		array_push( $this->buglist, $bug );
		
		// SAVE THAT FUCK MUAHAHAHAH oh lord I need sleep
		$io->save("buglist",$this->buglist); // fuck i'm so lazy. this will set bug id automatically
	}
	
	//=========================================//
	// Finds a bug and changes its status
	public function ChangeBugStatus ( $id, $status )
	{
		// Load up the bug list
		$io = new DarkBuglistIO();
		$this->buglist = $io->load("buglist");
		
		foreach ( $this->buglist AS $bug )
		{
			if ( $bug->id == $id )
			{
				if ( $bug->status != $status )
				{
					// Build the history data to add
					$statusString = "Unresolved";
					if ( $status == 1 ) {
						$statusString = "Unknown";
					}
					else if ( $status == 2 ) {
						$statusString = "Fixed";
					}
					$newhistory = array(
						"date" => date("Y-m-d H:i:s"),
						"info" => "Status changed to " . $statusString . " by " . $_SERVER['REMOTE_USER'],
						"detail" => $bug->status,
					);
					// Add it to the history
					array_push( $bug->history, $newhistory );
					
					// Set the status
					$bug->status = $status;
				}
				// found bug, break out of loop
				break;
			}
		}
		
		// save edited buglist
		$io->save("buglist",$this->buglist);
		
		// build bug details page
		$this->BuildBugDetails( $id );
	}
	
	//=========================================//
	// Change the bug description
	public function ChangeBugDescription ( $id, $description )
	{
		// Load up the bug list
		$io = new DarkBuglistIO();
		$this->buglist = $io->load("buglist");
		
		foreach ( $this->buglist AS $bug )
		{
			if ( $bug->id == $id )
			{
				// Create history entry with old description
				$newhistory = array(
					"date" => date("Y-m-d H:i:s"),
					"info" => "Description changed by " . $_SERVER['REMOTE_USER'],
					"detail" => $big->description,
				);
				// Add it to the history
				array_push( $bug->history, $newhistory );
				
				// Set new description
				$bug->description = $description;
				
				// found bug, break out of loop
				break;
			}
		}
		
		// save edited buglist
		$io->save("buglist",$this->buglist);
		
		// build bug details page
		$this->BuildBugDetails( $id );
	}
	
	//=========================================//
	// Finds a bug and changes its status
	public function GetBug ( $id )
	{
		// Load up the bug list
		$io = new DarkBuglistIO();
		$this->buglist = $io->load("buglist");
		
		foreach ( $this->buglist AS $bug ) {
			if ( $bug->id == $id ) {
				return $bug;
			}
		}
		return null;
	}

	//===============================================================================================//
	// Additional builders
	//===============================================================================================//
	
	//=========================================//
	protected function BuildMenu ()
	{
		echo( "<div class=\"menu-container\">" );
		echo( 
				"<div class=\"menu-group\">" .
					"<span class=\"menu-group-label\">" .
						"ACTIONS" .
					"</span>" .
					"<a href=\"\" onclick=\"return bugShowNewPanel();\">" .
						"<span class=\"menu-choice\">New Bug</span>" .
					"</a>" .
				"</div>" .
				"<div class=\"menu-group\">" .
					"<span class=\"menu-group-label\">" .
						"EXTRAS" .
					"</span>" .
					"<a href=\"\" onclick=\"return showBugs();\">" .
						"<span class=\"menu-choice\">Show Listing</span>" .
					"</a>" .
					"<a href=\"\" onclick=\"return showBugs(1);\">" .
						"<span class=\"menu-choice\">List Unresolved</span>" .
					"</a>" .
					"<a href=\"\" onclick=\"return showBugs(2);\">" .
						"<span class=\"menu-choice\">List Fixed</span>" .
					"</a>" .
				"</div>" 
			);
		echo( "</div>" );
	}
	//=========================================//
	// BuildListingEntry - builds listing for given input
	protected function BuildListing ( $type = 0 )
	{
		echo( "<div class=\"list-container\" id=\"submain-content\">" );
		// print top
		echo(
			"<div class=\"list-item\">" .
				'<span class="list-item-id"><span class="list-header">ID</span></span>' .
				'<span class="list-item-project"><span class="list-header">Project</span></span>' .
				'<span class="list-item-title"><span class="list-header">Title</span></span>' .
				'<span class="list-item-description"><span class="list-header">Description</span></span>' .
				'<span class="list-item-status"><span class="list-header">Status</span></span>' .
			"</div>" );
		for ( $statusmode = 0; $statusmode <= 2; $statusmode += 1 )
		{
			if ( $type == 1 && $statusmode == 2 ) {
				continue;
			}
			if ( $type == 2 && $statusmode != 2 ) {
				continue;
			}
			//foreach ( $this->buglist AS $bug )
			$index = count($this->buglist);
			while ( $index )
			{
				$bug = $this->buglist[--$index];
				if ( $bug->status != $statusmode ) {
					continue;
				}
				else {
					$this->BuildListingEntry($bug);
				}
			}
		}
		echo( "</div>" );
	}
	//=========================================//
	// BuildListingEntry - builds a list-item row for the given bug
	protected function BuildListingEntry ( $bug )
	{
		// Create status string
		$statusString = "Unresolved";
		if ( $bug->status == 1 ) {
			$statusString = "Unknown";
		}
		else if ( $bug->status == 2 ) {
			$statusString = "Fixed";
		}
		// Generate item properties
		$itemProperties = "";
		if ( $bug->status == 1 ) {
			$itemProperties = 'golden="1"';
		}
		if ( $bug->status == 0 ) {
			$itemProperties = 'error="1"';
		}
		// Echo actual line
		echo(
			'<a href="" onclick="return bugListingExpand('. $bug->id .');">' .
			'<div class="list-item" ' . $itemProperties . ' >' .
				"<span class=\"list-item-id\">" . $bug->id . "</span>" .
				"<span class=\"list-item-project\">" . $bug->project . "</span>" .
				"<span class=\"list-item-title\">" . strip_tags( $bug->title, '<b><i>' ) . "</span>" .
				"<span class=\"list-item-description\">" . strip_tags( $bug->description, '<b><i>' ) . "</span>" .
				'<span class="list-item-status" status="'.$bug->status.'">' . $statusString . '</span>' .
			"</div>" .
			"</a>"
			);
	}
}


?>