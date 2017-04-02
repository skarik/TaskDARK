<?php

require_once __DIR__ . "/calendar.php";
require_once __DIR__ . "/buglist.php";
require_once __DIR__ . "/tasklist.php";
require_once __DIR__ . "/idealist.php";
require_once __DIR__ . "/mainpage.php";

class DarkMainContent
{

	public function EmitNavigation ()
	{
		$user = $_SERVER['REMOTE_USER'];
		if ( $user == "" || $user == null ) {
			$user = "Public";
		}
		echo(
			'<div class="page-login-info">' .
				//'You are logged in as "' . $_SERVER['REMOTE_USER'] . '" at ' . $_SERVER['SERVER_NAME'] .
				'Logged in as "' . $_SERVER['REMOTE_USER'] . '"' .
			'</div>'
			);
		/*echo(
			'<div class="page-notice">' .
				'TaskDARK is currently under maintenance.' .
			'</div>'
			);*/
		echo(
			"<div class=\"navigation-container\">" .
				"<span class=\"navigation-option\"><a href=\"\" onclick=\"return showMainpage();\">Main Landing</a></span>" .
				"<span class=\"navigation-option\"><a href=\"\" onclick=\"return showCalendar();\">Calendar</a></span>" .
				"<span class=\"navigation-option\"><a href=\"\" onclick=\"return showTasks();\">Tasks</a></span>" .
				"<span class=\"navigation-option\"><a href=\"\" onclick=\"return showBugs();\">Bugs</a></span>" .
				"<span class=\"navigation-option\"><a href=\"\" onclick=\"return showIdeas();\">Ideas</a></span>" .
			"</div>"
			);
	}
	
	public function EmitFooter ()
	{
		echo(
			'<div class="page-footer">' .
				"SURGEON GENERAL WARNING: Loss of life is prevalent while using this software. Use at your own risk. Continued unsupervised use may cause unwanted pregnacy in males, and generate the ability to be taken seriously in others." .
			'</div>'
			);
	}
	
	public function EmitCalendar ()
	{
		$this->EmitNavigation();
		$calendar = new DarkCalendar();
		$calendar->Build();
		$this->EmitFooter();
	}
	public function EmitBuglist ( $type=0 )
	{
		$this->EmitNavigation();
		$buglist = new DarkBuglist();
		$buglist->Build($type);
		$this->EmitFooter();
	}
	public function EmitTasklist ()
	{
		$this->EmitNavigation();
		$tasklist = new DarkTasklist();
		$tasklist->Build();
		$this->EmitFooter();
	}
	public function EmitIdealist ( $type=0 )
	{
		$this->EmitNavigation();
		$idealist= new DarkIdealist();
		$idealist->Build($type);
		$this->EmitFooter();
	}
	public function EmitMainpage ()
	{
		$this->EmitNavigation();
		$mainpage = new DarkMainpage();
		$mainpage->Build();
		$this->EmitFooter();
	}
}

?>
