<?php
require_once __DIR__ . "/taskstructure.php";
require_once __DIR__ . "/../include/Parsedown.php";

class DarkTasklist
{
	public $tasklist;
	public $io;

	const STATUS_INPROGRESS = 0;
	const STATUS_ALMOSTDONE = 1;
	const STATUS_DOCUMENTATION = 2;
	const STATUS_DONE = 3;
	const STATUS_DEAD = 4;

	//=========================================//
	// Construct
	function __construct ()
	{
		$this->tasklist = null;
		$this->io = null;
	}
	//=========================================//
	// Other initialize
	public function Init ()
	{
		if ( $this->tasklist == null )
		{
			// Load up the task list
			$this->io = new DarkTasklistIO();
			$this->tasklist = $this->io->load("tasklist");
		}
	}
	//=========================================//
	// Output tasklist
	public function Build ()
	{
		$this->Init(); // Load up the task list

		$randomflavor = array("Keep on track", "Stay on path", "Never get lost", "Believe in yourself", "Do the developing");
		echo( '<div class="page-title">Tasks <sub>' . $randomflavor[array_rand($randomflavor)] . '</sub></div>' );

		$this->BuildMenu();
		$this->BuildListing();
	}

	//===============================================================================================//
	// Tasklist Editing/Query
	//===============================================================================================//

	//=========================================//
	//	void AddTask ( title, description, project ending_date )
	// Adds a new task to the list, given a title, desc, project, and ending timestamp
	public function AddTask ( $title, $description, $project, $enddate )
	{
		$this->Init(); // Load up the task list
		echo( 'SAVING: task "' . $title . '"' );

		// create new task
		$task = new DarkTask();
		$task->title = $title;
		$task->project = $project;
		$task->description = htmlspecialchars($description);
		$task->enddate = $enddate;
		// push it to the tasklist
		array_push( $this->tasklist, $task );

		// save it
		$this->Save();
	}

	//=========================================//
	//	DarkTask GetTask ( id )
	// Returns first task with matching ID
	public function GetTask ( $id )
	{
		$this->Init(); // Load up task list
		foreach ( $this->tasklist AS $task )
		{	// Find matching task
			if ( $task->id == $id ) {
				return $task;
			}
		}
		return null;
	}

	//=========================================//
	//	array<DarkTask> GetTasksOnDay ( day )
	// Returns all tasks that fall on the given day.
	// To do this, checks for all tasks within 24 hours of given time.
	public function GetTasksOnDay ( $daystamp )
	{
		$this->Init(); // Load up task list
		$tasks = array();
		foreach ( $this->tasklist AS $task )
		{
			if ( abs( $daystamp - $task->enddate ) < (12 * 60 * 60) ) {
				array_push( $tasks, $task );
			}
		}
		return $tasks;
	}

	//=========================================//
	//	void TaskChangeStatus ( id, new_status )
	// Changes status of given task
	public function TaskChangeStatus ( $id, $status )
	{
		$this->Init(); // Load up task list
		foreach ( $this->tasklist AS $task )
		{	// Find task with matching id, and edit it
			if ( $task->id == $id ) {
				$task->status = $status;
				break;
			}
		}
		// save it
		$this->Save();
		// Refresh page
		$this->BuildDetails( $id );
	}

	//=========================================//
	//	void TaskChangeDescription ( id, new_description )
	// Changes description of given task
	public function TaskChangeDescription ( $id, $description )
	{
		$this->Init(); // Load up task list
		foreach ( $this->tasklist AS $task )
		{	// Find task with matching id, and edit it
			if ( $task->id == $id ) {
				$task->description = $description;
				break;
			}
		}
		// save it
		$this->Save();
		// Refresh page
		$this->BuildDetails( $id );
	}

	//=========================================//
	//	void TaskPushEnddate ( id, new_date )
	// Changes date of given task. Will not change it to anything earlier.
	public function TaskPushEnddate ( $id, $date )
	{
		$enddate = intval($date);
		$this->Init(); // Load up task list
		foreach ( $this->tasklist AS $task )
		{	// Find task with matching id, and edit it
			if ( $task->id == $id ) {
				if ( $task->enddate < $enddate ) {
					$task->enddate = $enddate;
					$task->delays += 1;
				}
				else {
					echo( '<div class="page-notice">The given date was before the current set date. You cannot turn back time!</div>' );
				}
				break;
			}
		}
		// save it
		$this->Save();
		// Refresh page
		$this->BuildDetails( $id );
	}

	//=========================================//
	//	void Save ()
	// Saves the list.
	protected function Save ( )
	{
		$this->Init();
		$this->io->save("tasklist",$this->tasklist);
	}

	//===============================================================================================//
	// Panels
	//===============================================================================================//

	//=========================================//
	// Output bug details
	public function BuildDetails ( $id )
	{
		$this->Init(); // Load up task list

		$thisTime = time();
		$task = $this->GetTask( $id );

		// make the progress string
		$statusString = "In Progress";
		if ( $task->status == self::STATUS_ALMOSTDONE ) {
			$statusString = "Probably Done";
		}
		else if ( $task->status == self::STATUS_DOCUMENTATION ) {
			$statusString = '"Writing Documentation"';
		}
		else if ( $task->status == self::STATUS_DONE ) {
			$statusString = "Finished";
		}
		else if ( $task->status == self::STATUS_DEAD ) {
			$statusString = "Way in the past";
		}
		if ( $task->status < self::STATUS_DONE ) {
			if ( abs( $thisTime - $task->enddate ) < (12 * 60 * 60) ) {
				$statusString = $statusString . " (Due Today)";
			}
			else if ( $thisTime > $task->enddate ) {
				$statusString = $statusString . " (LATE)";
			}
		}
		// make the delay string
		$delayString = "";
		if ( $task->delays > 0 ) {
			$delayString = "<br>";
			if ( $task->delays == 1 ) {
				$delayString = $delayString . "Delayed once.";
			}
			else {
				$delayString = $delayString . "Delayed " . $task->delays . " times.";
			}
		}

		// echo output
        $Parsedown = new Parsedown();
		echo(
			'<div class="detail-container">' .
				'<div class="detail-heading" id="info-title">' .
					$task->title .
				'</div>' .
				'<div class="detail-info" id="info-description">' .
					//nl2br($task->description) .
                    $Parsedown->text($task->description) .
					'<a href="#" onclick="return taskChangeDescription('.$id.')">' .
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
					'<a href="#" onclick="return taskChangeStatus('.$id.')">' .
					'<div class="detail-button">' .
						"(change)" .
					'</div>' .
					'</a>' .
				'</div>' .
				'<div class="detail-heading">' .
					"DUE DATE" .
				'</div>' .
				'<div class="detail-info" id="info-duedate">' .
					date("Y-m-d",$task->enddate) . $delayString .
					'<a href="#" onclick="return taskChangeEnddate('.$id.')">' .
					'<div class="detail-button">' .
						"(push back)" .
					'</div>' .
					'</a>' .
				'</div>' .
			'</div>'
			);
		// done
	}

	//=========================================//
	// Output a new bug panel
	public function BuildPanelNew ()
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
				'<div class="input-box-heading">End Date (YYYY-MM-DD)</div>' .
				'<textarea class="input-box-oneline" id="input-date" rows="1" cols="200" name="input-date">'.date('Y-m-d').'</textarea>' .
				'<a href="#" onclick="return taskNewTask();">' .
					'<div class="input-box-submit">Submit</div>' .
				'</a>'
			);
		echo( "</div>" );
	}

	//=========================================//
	// Output status edit panel
	public function BuildPanelStatus ( $id )
	{
		$task = $this->GetTask( $id );
		// print 4 buttons, each referring to the proper
		$menu = array( 'IN PROGRESS', 'PROBABLY DONE?', '"WRITING DOCUMENTATION"', 'FINISHED', 'THIS NEVER HAPPENED' );
		echo( "<div>Choose a new status for this task.</div>" );
		for ( $i = 0; $i < 5; $i += 1 )
		{
			echo( '<a href="" onclick="return taskSendChangeStatus('.$id.','.$i.');">' );
			if ( $task->status == $i ) { // different style if current selection
				echo( '<span class="menu-choice-current">' );
			}
			else {
				echo( '<span class="menu-choice">' );
			}
			echo( $menu[$i] );
			echo( '</span></a>' );
		}
	}

	//=========================================//
	// Output description edit panel
	public function BuildPanelDescription ( $id )
	{
		$task = $this->GetTask( $id );
		// print the fuckin text box
		echo(
			'<textarea class="input-box-multiline" id="input-description" rows="10" cols="200" name="input-description">' .
				$task->description .
			'</textarea>'
			);

		// print confirm button
		echo(
			'<a href="#" onclick="return taskSendChangeDescription('.$id.');">' .
				'<span class="menu-choice">Submit Change</span>' .
			'</a>'
			);
	}

	//=========================================//
	// Output date pushing panel
	public function BuildPanelDatepush ( $id )
	{
		$task = $this->GetTask( $id );
		// print the text box
		echo(
			'<textarea class="input-box-oneline" id="input-date" rows="1" cols="200" name="input-date">' .
				date('Y-m-d',$task->enddate) .
			'</textarea>'
			);

		// print confirm button
		echo(
			'<a href="#" onclick="return taskSendChangeEnddate('.$id.');">' .
				'<span class="menu-choice">Submit Change</span>' .
			'</a>'
			);
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
					"<a href=\"\" onclick=\"return taskShowNewPanel();\">" .
						"<span class=\"menu-choice\">New Task</span>" .
					"</a>" .
				"</div>" .
				"<div class=\"menu-group\">" .
					"<span class=\"menu-group-label\">" .
						"EXTRAS" .
					"</span>" .
					"<a href=\"\" onclick=\"return showTasks();\">" .
						"<span class=\"menu-choice\">Show Listing</span>" .
					"</a>" .
				"</div>"
			);
		echo( "</div>" );
	}

	//=========================================//
	// BuildListingEntry - builds listing for given input
	protected function BuildListing ( )
	{
		$thisTime = time();

		echo( "<div class=\"list-container\" id=\"submain-content\">" );
		// print top
		echo(
			"<div class=\"list-item\">" .
				'<span class="list-item-title"><span class="list-header">Title</span></span>' .
				'<span class="list-item-project"><span class="list-header">Project</span></span>' .
				//'<span class="list-item-description"><span class="list-header">Description</span></span>' .
				'<span class="list-item-status"><span class="list-header">Status</span></span>' .
				'<span class="list-item-status"><span class="list-header">Delayed</span></span>' .
				'<span class="list-item-date"><span class="list-header">Start Date</span></span>' .
				'<span class="list-item-date"><span class="list-header">End Date</span></span>' .
			"</div>" );
		for ( $lateMode = 0; $lateMode <= 3; $lateMode += 1 )
		{
			$index = count($this->tasklist);
			while ( $index )
			{
				$task = $this->tasklist[--$index];

				// move late tasks to the tippity top
				$isLate = ( $thisTime > $task->enddate && $task->status < self::STATUS_DONE );
				if ( ($lateMode == 0 && !$isLate) || ($lateMode == 1 && $isLate) ) {
					continue;
				}
                // move done stuff below
                if ( ($lateMode == 2 && $task->status != self::STATUS_DONE) || ($lateMode != 2 && $task->status == self::STATUS_DONE) ) {
					continue;
				}
				// and move archived stuff to the very bottom
				if ( ($lateMode == 3 && $task->status != self::STATUS_DEAD) || ($lateMode != 3 && $task->status == self::STATUS_DEAD) ) {
					continue;
				}

                // print out the task now
                $this->BuildListingEntry( $task, $thisTime );
			}
		}
		echo( "</div>" );
	}
	//=========================================//
	// BuildListingEntry - builds a list-item row for the given task
	protected function BuildListingEntry ( $task, $compareTime )
    {
        // make the progress string
        $statusString = "In Progress";
        if ( $task->status == self::STATUS_ALMOSTDONE ) {
            $statusString = "Probably Done";
        }
        else if ( $task->status == self::STATUS_DOCUMENTATION ) {
            $statusString = '"Documentation"';
        }
        else if ( $task->status == self::STATUS_DONE ) {
            $statusString = "Finished";
        }
        else if ( $task->status == self::STATUS_DEAD ) {
            $statusString = "Way in the past";
        }
        if ( $task->status < self::STATUS_DONE ) {
            if ( abs( $compareTime - $task->enddate ) < (24 * 60 * 60) ) {
                $statusString = "Due Today";
            }
            else if ( $compareTime > $task->enddate ) {
                $statusString = "LATE";
            }
        }
        // make the delay string
        $delayString = "";
        if ( $task->delays == 1 ) {
            $delayString = "Once";
        }
        else if ( $task->delays > 1 ) {
            $delayString = $task->delays . " times";
        }
        else if ( $statusString == "LATE" ) {
            $delayString = "Will be";
        }

        // create the item props
        $itemProperties = "";
		if ( $statusString == "Due Today" )
		{
			$itemProperties = 'golden="1"';
		}
        if ( $statusString == "LATE" )
        {
            $itemProperties = 'error="1"';
        }
        if ( $task->status == self::STATUS_DONE )
        {
            $itemProperties = 'past="1"';
        }
        if ( $task->status == self::STATUS_DEAD )
        {
            $itemProperties = 'past="2"';
        }

        // build listing
        echo(
                '<a href="" onclick="return taskListingExpand('. $task->id .');">' .
                    '<div class="list-item" ' . $itemProperties . ' >' .
                        "<span class=\"list-item-title\">" . strip_tags( $task->title, '<b><i>' ) . "</span>" .
                        "<span class=\"list-item-project\">" . $task->project . "</span>" .
                        //"<span class=\"list-item-description\">" . strip_tags( $task->description,'<b><i>') . "</span>" .
                        '<span class="list-item-status" status="'.$task->status.'">' . $statusString . '</span>' .
                        "<span class=\"list-item-status\">" . $delayString . "</span>" .
                        "<span class=\"list-item-date\">" . date("Y-m-d",$task->startdate) . "</span>" .
                        "<span class=\"list-item-date\">" . date("Y-m-d",$task->enddate) . "</span>" .
                    "</div>" .
                "</a>"
            );

    }

}


?>
