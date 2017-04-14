<?php
require_once __DIR__ . "/tasklist.php";

class DarkMainpage
{
	public $m_month;
	public $m_year;
	public $m_day;

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
        $randomflavor = array(
            "It's probably sunny outside",
            "Call someone you care about and tell them how much they mean to you",
            "Smell the bees and kiss the flowers",
            "",
            "Great weather today",
            "Another perfect day",
            "Best day, better than all the other days",
            "",
            "Only thing better than today are our games");

		echo( '<div class="page-title">' . $this->m_day . " <sub>" . $monthName . " " . $this->m_year . " <sub style='margin-left:10%;'>" . $randomflavor[array_rand($randomflavor)] . "</sub></sub></div>" );


		$this->EmitMainpage();
	}

	protected function EmitMainpage ()
	{
		echo( "<div class=\"page-content\">" );
		$htmlfile = __DIR__ . "/../content/main_landing.html";
		if ( readfile( $htmlfile ) == FALSE )
		{
			echo( "Could not find landing page: \"" . $htmlfile . "\"" );
		}
		echo( "</div>" );
	}
}




?>
