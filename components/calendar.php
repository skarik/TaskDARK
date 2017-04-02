<?php
require_once __DIR__ . "/tasklist.php";

class DarkCalendar
{
	public $m_month;
	public $m_year;
	public $m_day;
	// startOnMonday
	// if true, calendar starts on Monday. Otherwise, starts on Sunday.
	public $m_startOnMonday = FALSE;
	
	public $tasks;

	//=========================================//
	// Construct
	function __construct ()
	{
		$this->m_day = date("j");
		$this->m_month = date("n");
		$this->m_year = date("Y");
	}
	
	//=========================================//
	// Output calendar
	public function Build ()
	{
		$dateObj = DateTime::createFromFormat( '!m', $this->m_month );
	
		// Get the month
		$monthName = $dateObj->format('F');
		
		// Get the tasks
		$this->tasks = new DarkTasklist();
		$this->tasks->Init();
		
		// Output the top
		echo( '<div class="page-title">' . $monthName . " " . $this->m_year . "</div>" );
		
		$this->CreateCalendar();
	}
	
	protected function CreateCalendar ()
	{
		// Create the month reference point
		$timestamp = mktime( 0,0,0, $this->m_month, 1, $this->m_year );
		
		$maxday = date("t",$timestamp);
		$thismonth = getdate( $timestamp );
		$startday = $thismonth['wday'];
		$calendarLength = ceil( ($maxday+$startday)/7.0 ) * 7.0;
		
		$dayoffset = 0;
		if ( $this->m_startOnMonday ) {
			$dayoffset = 1;
		}
		
		// Print out the week days
		echo( "<div class=\"calendar-week\">" );
		for ( $i = $dayoffset; $i < 7+$dayoffset; $i += 1 )
		{
			echo(
				"<span class=\"calendar-dayname\">" .
					date('l', strtotime("Sunday +{$i} days")) .
				"</span>"
				);
		}
		echo( "</div>" );
		// Print out the calendar days
		for ( $i = 0; $i < $calendarLength; $i += 1 )
		{
			if ( ($i % 7) == 0 ) {
				echo( "<div class=\"calendar-week\">" );
			}
			if ( ($i < $startday) || ($i >= ($maxday+$startday)) ) {
				echo( "<span class=\"calendar-day-othermonth\">" . "</span>" );
			}
			else
			{	// Print actual day of the month
				$thisDay = ($i - $startday + 1);
				if ( $this->m_day == $thisDay ) {
					echo( '<span class="calendar-day-today">' );
				}
				else {
					echo( '<span class="calendar-day">' );
				}
				echo (
						"<div class=\"calendar-day-heading\">" . $thisDay . "</div>" .
					"" );
				
				// print all tasks for this day
				$tasklist = $this->tasks->GetTasksOnDay( $timestamp + ($thisDay*(24 * 60 * 60)) - (12 * 60 * 60) );
				foreach ( $tasklist AS $task )
				{
					echo (
						'<a href="#" onclick="return navigateToTask('.$task->id.');" >' .
						'<div class="list-item">' .
							$task->title .
						"</div>" .
						'</a>'
						);
				}
				
				echo ( "</span>" );
			}
			if ( ($i % 7) == 6 ) {
				echo( "</div>" );
			}
		}
	}
}




?>