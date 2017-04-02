<?php
require_once __DIR__ . "/../config/config.php"; // Include taskdark config
require_once __DIR__ . "/ideastructure.php"; // Include idea info

class DarkIdealist
{
	public $idealist;
	public $io;
	
	const STATUS_AWAITING = 0;
	const STATUS_REJECTED = 1;
	const STATUS_ACCEPTED = 2;
	const STATUS_IMPLEMENTED = 3;
	
	//=========================================//
	// Output idealist
	public function Build ( $type = 0 )
	{
		// Load up the idea list
		$io = new DarkIdealistIO();
		$this->idealist = $io->load("idealist");
		$io->save("idealist", $this->idealist);
		
		echo( "<div class=\"page-title\">Ideas <sub>Propose features</sub></div>" );
		
		$this->BuildMenu();
		$this->BuildListing( $type );
	}
	
	//===============================================================================================//
	// Detail panels
	//===============================================================================================//
	
	//=========================================//
	// Output idea details
	public function BuildIdeaDetails ( $id )
	{
		// Load up the idea list
		$io = new DarkIdealistIO();
		$this->idealist = $io->load("idealist");
		
		// Loop through and find matching ID
		foreach ( $this->idealist AS $idea )
		{
			if ( $idea->id == $id )
			{
				$statusString = $GLOBALS["TASKDARK_PROPOSAL_STATUS"][intval($idea->status)];
				$historyString = "";
				foreach ( $idea->history AS $pasts )
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
							$idea->title .
						'</div>' .
						'<div class="detail-info" id="info-description">' .
							nl2br($idea->description) .
							'<a href="" onclick="return ideaChangeDescription('.$id.')">' .
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
							'<a href="" onclick="return ideaChangeStatus('.$id.')">' .
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
				// found idea, break out of loop
				break;
			}
		}
	}
	
	//=========================================//
	// Output a new idea panel
	public function BuildNewideaPanel ()
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
				'<div class="input-box-heading">Proposal</div>' .
				'<textarea class="input-box-multiline" id="input-description" rows="10" cols="200" name="input-description"></textarea>' .
				'<br><br>' .
				'<div>Proposals do not record who submitted them. The proposal will be reviewed by a project administrator.</div>' .
				'<br>' .
				'<a href="" onclick="return ideaNewIdea();">' .
					'<div class="input-box-submit">Submit</div>' .
				'</a>' .
				'<a href="" onclick="return showIdeas();">' .
					'<div class="input-box-submit">Discard and Cancel</div>' .
				'</a>' 
			);
		echo( "</div>" );
	}
	
	//=========================================//
	// Output status change panel
	public function BuildChangeStatusPanel ( $id )
	{	
		$idea = $this->GetIdea( $id );
		$statusTypes = array( 'WAITING REVIEW', 'REJECTED', 'ACCEPTED', 'IMPLEMENTED' );
		if ( IsAdmin( $idea->project ) )
		{
			echo( "<div>This change will be recorded.</div>" );
			// print 4 buttons, each referring to the status
			for ( $i = 0; $i < 4; $i += 1 )
			{
				echo( '<a href="" onclick="return ideaSendChangeStatus('.$id.','.$i.');">' );
				if ( $idea->status == $i ) {
					echo( '<span class="menu-choice-current">' );
				}
				else {
					echo( '<span class="menu-choice">' );
				}
				echo( $statusTypes[$i] . "</span></a>" );
			}
		}
		else
		{
			// Print the current status, as well as a "Don't do that!" message
			echo( "<div>You do not have permission to change the status.</div>" );
			echo( '<span class="menu-choice-current">' );
			echo( $statusTypes[$idea->status] . "</span>" );
		}
	}
	
	//=========================================//
	// Output description change panel
	public function BuildChangeDescriptionPanel ( $id )
	{
		// Load up the idea list
		$io = new DarkIdealistIO();
		$this->idealist = $io->load("idealist");
		
		$descriptionString = "";
		foreach ( $this->idealist AS $idea )
		{
			if ( $idea->id == $id )
			{
				$descriptionString = $idea->description;
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
			'<a href="#" onclick="return ideaSendChangeDescription('.$id.');">' .
				'<span class="menu-choice">Submit Change</span>' .
			'</a>'
			);
	}
	
	//===============================================================================================//
	// List editing
	//===============================================================================================//

	//=========================================//
	// Adds a new idea to the list. Fucking crazy.
	public function AddIdea ( $title, $description, $project )
	{
		// Load up the idea list
		$io = new DarkIdealistIO();
		$this->idealist = $io->load("idealist");
		
		echo( $title . "::::" . $description );
		
		// create new idea
		$idea = new DarkIdea();
		$idea->title = $title;
		$idea->description = htmlspecialchars($description);
		$idea->project = $project;
		$newhistory = array(
			"date" => date("Y-m-d H:i:s"),
			"info" => "Idea created.",
			"detail" => "",
		);
		// Add it to the history
		array_push( $idea->history, $newhistory );
		// push it to the list kekekekek
		array_push( $this->idealist, $idea );
		
		// Save the idea list
		$io->save("idealist",$this->idealist);
	}
	
	//=========================================//
	// Finds a idea and changes its status
	public function ChangeIdeaStatus ( $id, $status )
	{
		// Load up the idea list
		$io = new DarkIdealistIO();
		$this->idealist = $io->load("idealist");
		
		foreach ( $this->idealist AS $idea )
		{
			if ( $idea->id == $id )
			{
				if ( $idea->status != $status )
				{
					// Build the history data to add
					$statusString = $GLOBALS["TASKDARK_PROPOSAL_STATUS"][$status];
					$newhistory = array(
						"date" => date("Y-m-d H:i:s"),
						"info" => "Status changed to '" . $statusString . "' by " . $_SERVER['REMOTE_USER'],
						"detail" => $idea->status,
					);
					// Add it to the history
					array_push( $idea->history, $newhistory );
					
					// Set the status
					$idea->status = $status;
				}
				// found idea, break out of loop
				break;
			}
		}
		
		// save edited idealist
		$io->save("idealist",$this->idealist);
		
		// build idea details page
		$this->BuildIdeaDetails( $id );
	}
	
	//=========================================//
	// Change the idea description
	public function ChangeIdeaDescription ( $id, $description )
	{
		// Load up the idea list
		$io = new DarkIdealistIO();
		$this->idealist = $io->load("idealist");
		
		foreach ( $this->idealist AS $idea )
		{
			if ( $idea->id == $id )
			{
				// Create history entry with old description
				$newhistory = array(
					"date" => date("Y-m-d H:i:s"),
					"info" => "Proposal changed by " . $_SERVER['REMOTE_USER'],
					"detail" => $idea->description,
				);
				// Add it to the history
				array_push( $idea->history, $newhistory );
				
				// Set new description
				$idea->description = $description;
				
				// found idea, break out of loop
				break;
			}
		}
		
		// save edited idealist
		$io->save("idealist",$this->idealist);
		
		// build idea details page
		$this->BuildIdeaDetails( $id );
	}
	
	//=========================================//
	// Finds a idea based on ID and changes its status
	public function GetIdea ( $id )
	{
		// Load up the idea list
		$io = new DarkIdealistIO();
		$this->idealist = $io->load("idealist");
		
		foreach ( $this->idealist AS $idea ) {
			if ( $idea->id == $id ) {
				return $idea;
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
					"<a href=\"\" onclick=\"return ideaShowNewPanel();\">" .
						"<span class=\"menu-choice\">New Proposal</span>" .
					"</a>" .
				"</div>" .
				"<div class=\"menu-group\">" .
					"<span class=\"menu-group-label\">" .
						"EXTRAS" .
					"</span>" .
					"<a href=\"\" onclick=\"return showIdeas(0);\">" .
						"<span class=\"menu-choice\">Show Proposals</span>" .
					"</a>" .
					"<a href=\"\" onclick=\"return showIdeas(1);\">" .
						"<span class=\"menu-choice\">List Unreviewed</span>" .
					"</a>" .
					"<a href=\"\" onclick=\"return showIdeas(2);\">" .
						"<span class=\"menu-choice\">List Accepted</span>" .
					"</a>" .
				"</div>" 
			);
		echo( "</div>" );
	}
	//=========================================//
	protected function BuildListing ( $type = 0 )
	{
		echo( "<div class=\"list-container\" id=\"submain-content\">" );
		// print top
		echo(
			"<div class=\"list-item\">" .
				'<span class="list-item-project"><span class="list-header">Project</span></span>' .
				'<span class="list-item-title"><span class="list-header">Title</span></span>' .
				'<span class="list-item-description"><span class="list-header">Proposal</span></span>' .
				'<span class="list-item-status"><span class="list-header">Status</span></span>' .
			"</div>" );
			
		for ( $i = 0; $i < 4; $i += 1 )
		{
			$statusmode = self::STATUS_AWAITING;
			if ($i == 1) $statusmode = self::STATUS_ACCEPTED;
			if ($i == 2) $statusmode = self::STATUS_IMPLEMENTED;
			if ($i == 3) $statusmode = self::STATUS_REJECTED;
			
			if ( $type == 1 && $statusmode != self::STATUS_AWAITING ) {
				continue;
			}
			if ( $type == 2 && $statusmode != self::STATUS_ACCEPTED && $statusmode != self::STATUS_IMPLEMENTED ) {
				continue;
			}
			
			$index = count($this->idealist);
			while ( $index )
			{
				$idea = $this->idealist[--$index];
				if ( $idea->status != $statusmode ) {
					continue;
				}
				else {
					$this->BuildListingEntry($idea);
				}
			}
		}
		echo( "</div>" );
	}
	//=========================================//
	// BuildListingEntry - builds a list-item row for the given idea
	protected function BuildListingEntry ( $idea )
	{
		// Generate status string
		$statusString = $GLOBALS["TASKDARK_PROPOSAL_STATUS"][$idea->status];
		// Generate status color
		$statusColor = 1;
		if ( $idea->status == self::STATUS_ACCEPTED ) $statusColor = 2;
		else if ( $idea->status == self::STATUS_IMPLEMENTED ) $statusColor = 4;
		else if ( $idea->status == self::STATUS_REJECTED ) $statusColor = 0;
		// Generate item properties
		$itemProperties = "";
		if ( $idea->status == self::STATUS_ACCEPTED ) {
			$itemProperties = 'golden="1"';
		}
		if ( $idea->status == self::STATUS_REJECTED ) {
			$itemProperties = 'error="1"';
		}
		// Generate actual HTML
		echo(
			'<a href="" onclick="return ideaListingExpand('. $idea->id .');">' .
			'<div class="list-item" ' . $itemProperties . ' >' .
				"<span class=\"list-item-project\">" . $idea->project . "</span>" .
				"<span class=\"list-item-title\">" . strip_tags( $idea->title, '<b><i>' ) . "</span>" .
				"<span class=\"list-item-description\">" . strip_tags( $idea->description, '<b><i>' ) . "</span>" .
				'<span class="list-item-status" status="'.$statusColor.'">' . $statusString . '</span>' .
			"</div>" .
			"</a>"
			);
	}
}

?>