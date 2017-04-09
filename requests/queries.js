
//===============================================================================================//
// Main Content
//===============================================================================================//

// Grab the main content
var m_maincontent = document.getElementById( "main-content" );
var m_submaincontent;

function showCalendar ()
{
	ajaxRequestMainContent( "./requests/indexquery.php?page=calendar" );
	return false;
}
function showTasks ()
{
	ajaxRequestMainContent( "./requests/indexquery.php?page=tasks" );
	return false;
}
function showBugs (type = 0)
{
	ajaxRequestMainContent( "./requests/indexquery.php?page=bugs&type=" + type );
	return false;
}
function showIdeas (type = 0)
{
	ajaxRequestMainContent( "./requests/indexquery.php?page=ideas&type=" + type );
	return false;
}
function showMainpage ()
{
	ajaxRequestMainContent( "./requests/indexquery.php?page=mainpage" );
	return false;
}

function ajaxRequestMainContent ( request )
{
	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_maincontent.innerHTML = ajaxRequest.responseText;
		}
	}
	ajaxRequest.open( "GET", request, true );
	ajaxRequest.send();
}

//===============================================================================================//
// Main Content
//===============================================================================================//
function navigateToTask ( taskId )
{
	var savedTaskId = taskId;
	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_maincontent.innerHTML = ajaxRequest.responseText;
			taskListingExpand( savedTaskId );
		}
	}
	ajaxRequest.open( "GET", "./requests/indexquery.php?page=tasks", true );
	ajaxRequest.send();

	return false;
}


//===============================================================================================//
// Bug listing
//===============================================================================================//

function bugListingExpand ( bugId )
{
	ajaxRequestSubMainContent( "./requests/bugquery.php?cmd=detailbug&id="+bugId );
	return false;
}
function bugShowNewPanel ( )
{
	ajaxRequestSubMainContent( "./requests/bugquery.php?cmd=showbug_add_panel" );
	return false;
}
function ajaxRequestSubMainContent ( request )
{
	// Update submain
	m_submaincontent = document.getElementById( "submain-content" );

	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_submaincontent.innerHTML = ajaxRequest.responseText;
		}
	}
	ajaxRequest.open( "GET", request, true );
	ajaxRequest.send();
}

// Send request for description change panel
function bugChangeDescription ( bugId )
{
	var m_tempContent = document.getElementById( "info-description" );

	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_tempContent.innerHTML = ajaxRequest.responseText;
		}
	}
	ajaxRequest.open( "GET", "./requests/bugquery.php?cmd=change_description_panel&id="+bugId, true );
	ajaxRequest.send();

	return false;
}
// Send request for status change panel
function bugChangeStatus ( bugId )
{
	var m_tempContent = document.getElementById( "info-status" );

	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_tempContent.innerHTML = ajaxRequest.responseText;
		}
	}
	ajaxRequest.open( "GET", "./requests/bugquery.php?cmd=change_status_panel&id="+bugId, true );
	ajaxRequest.send();

	return false;
}
// Send request for severity change panel
function bugChangeSeverity ( bugId )
{
	var m_tempContent = document.getElementById( "info-severity" );

	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_tempContent.innerHTML = ajaxRequest.responseText;
		}
	}
	ajaxRequest.open( "GET", "./requests/bugquery.php?cmd=change_severity_panel&id="+bugId, true );
	ajaxRequest.send();

	return false;
}
// Send request for priority change panel
function bugChangePriority ( bugId )
{
	var m_tempContent = document.getElementById( "info-priority" );

	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_tempContent.innerHTML = ajaxRequest.responseText;
		}
	}
	ajaxRequest.open( "GET", "./requests/bugquery.php?cmd=change_priority_panel&id="+bugId, true );
	ajaxRequest.send();

	return false;
}


function bugNewBug ( )
{
	ajaxBugNewSend(
		encodeURIComponent(document.getElementById("input-title").value),
		encodeURIComponent(document.getElementById("input-description").value),
		encodeURIComponent(document.getElementById("input-project").value),
		encodeURIComponent(document.getElementById("input-severity").value),
		encodeURIComponent(document.getElementById("input-priority").value),
		encodeURIComponent(document.getElementById("input-entertaining").checked.toString()),
		encodeURIComponent(document.getElementById("input-assignee").value),
		);
	return false;
}
function ajaxBugNewSend ( title, description, project, severity, priority, entertaining, assignee )
{	// SO EASY TO BREAK
	// WHERE IS THE SECURITY? NOWHERE LOL
	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_submaincontent.innerHTML = ajaxRequest.responseText;
			showBugs();
		}
	}
	ajaxRequest.open( "POST", "./requests/bugquery.php", true );
	ajaxRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	ajaxRequest.send(
        "cmd=addbug&title="+title+"&description="+description+"&project="+project+
        "&severity="+severity+"&priority="+priority+"&entertaining="+entertaining+"&assignee="+assignee);
}

// send changed description
function bugSendChangeStatus ( bugId, status )
{
	ajaxRequestSubMainContent( "./requests/bugquery.php?cmd=change_bug_status&id="+bugId+"&status="+status );
	return false;
}
function bugSendChangeDescription ( bugId )
{
	m_submaincontent = document.getElementById( "submain-content" );
	var m_description = encodeURIComponent(document.getElementById("input-description").value);

	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_submaincontent.innerHTML = ajaxRequest.responseText;
		}
	}
	ajaxRequest.open( "POST", "./requests/bugquery.php", true );
	ajaxRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	ajaxRequest.send("cmd=change_bug_description&id="+bugId+"&description="+m_description);

	return false;
}
function bugSendChangePriority ( bugId, priority )
{
	ajaxRequestSubMainContent( "./requests/bugquery.php?cmd=change_bug_priority&id="+bugId+"&priority="+priority );
	return false;
}
function bugSendChangeSeverity ( bugId, severity )
{
	ajaxRequestSubMainContent( "./requests/bugquery.php?cmd=change_bug_severity&id="+bugId+"&severity="+severity );
	return false;
}


//===============================================================================================//
// Idea listing
//===============================================================================================//

function ideaListingExpand ( bugId )
{
	ajaxRequestSubMainContent( "./requests/ideaquery.php?cmd=detailidea&id="+bugId );
	return false;
}
function ideaShowNewPanel ( )
{
	ajaxRequestSubMainContent( "./requests/ideaquery.php?cmd=showidea_add_panel" );
	return false;
}
function ideaSendChangeStatus ( ideaId, status )
{
	ajaxRequestSubMainContent( "./requests/ideaquery.php?cmd=change_idea_status&id="+ideaId+"&status="+status );
	return false;
}

// Send request for description change panel
function ideaChangeDescription ( bugId )
{
	var m_tempContent = document.getElementById( "info-description" );

	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_tempContent.innerHTML = ajaxRequest.responseText;
		}
	}
	ajaxRequest.open( "GET", "./requests/ideaquery.php?cmd=change_description_panel&id="+bugId, true );
	ajaxRequest.send();

	return false;
}
// Send request for status change panel
function ideaChangeStatus ( bugId )
{
	var m_tempContent = document.getElementById( "info-status" );

	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_tempContent.innerHTML = ajaxRequest.responseText;
		}
	}
	ajaxRequest.open( "GET", "./requests/ideaquery.php?cmd=change_status_panel&id="+bugId, true );
	ajaxRequest.send();

	return false;
}


function ideaNewIdea ( )
{
	ajaxIdeaNewSend(
		encodeURIComponent(document.getElementById("input-title").value),
		encodeURIComponent(document.getElementById("input-description").value),
		encodeURIComponent(document.getElementById("input-project").value)
		);
	return false;
}
function ajaxIdeaNewSend ( title, description, project )
{	// SO EASY TO BREAK
	// WHERE IS THE SECURITY? NOWHERE LOL
	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_submaincontent.innerHTML = ajaxRequest.responseText;
			showIdeas();
		}
	}
	ajaxRequest.open( "POST", "./requests/ideaquery.php", true );
	ajaxRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	ajaxRequest.send("cmd=addidea&title="+title+"&description="+description+"&project="+project);
}

// send changed description
function ideaSendChangeDescription ( bugId )
{
	m_submaincontent = document.getElementById( "submain-content" );
	var m_description = encodeURIComponent(document.getElementById("input-description").value);

	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_submaincontent.innerHTML = ajaxRequest.responseText;
		}
	}
	ajaxRequest.open( "POST", "./requests/ideaquery.php", true );
	ajaxRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	ajaxRequest.send("cmd=change_idea_description&id="+bugId+"&description="+m_description);

	return false;
}

//===============================================================================================//
// Task listing
//===============================================================================================//

function taskListingExpand ( taskId )
{
	ajaxRequestSubMainContent( "./requests/taskquery.php?cmd=detail&id="+taskId );
	return false;
}
function taskShowNewPanel ( )
{
	ajaxRequestSubMainContent( "./requests/taskquery.php?cmd=panel_newtask" );
	return false;
}

function taskNewTask ( )
{
	var cmd;
	cmd =
		"cmd=addtask&title=" +
		encodeURIComponent(document.getElementById("input-title").value) +
		"&description=" +
		encodeURIComponent(document.getElementById("input-description").value) +
		"&project=" +
		encodeURIComponent(document.getElementById("input-project").value) +
		"&endtime=" +
		((new Date(document.getElementById("input-date").value)).getTime()/1000 + (12 * 60 * 60));

	ajaxRequestSubMainContentPost( "./requests/taskquery.php", cmd, "showTasks" );
	return false;
}

function ajaxRequestSubMainContentPost ( request, command, endTask )
{
	// Update submain
	m_submaincontent = document.getElementById( "submain-content" );
	// grab task to do after
	var postTask = endTask;

	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_submaincontent.innerHTML = ajaxRequest.responseText;
			if ( postTask != undefined && postTask != null ) {
				window[postTask]();
			}
		}
	}
	ajaxRequest.open( "POST", request, true );
	ajaxRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	ajaxRequest.send(command);
}


// Send request for description change panel
function taskChangeDescription ( taskId )
{
	ajaxGetRequestToElement( "./requests/taskquery.php?cmd=panel_changedescription&id="+taskId, document.getElementById( "info-description" ) );
	return false;
}
// Send request for status change panel
function taskChangeStatus ( taskId )
{
	ajaxGetRequestToElement( "./requests/taskquery.php?cmd=panel_changestatus&id="+taskId, document.getElementById( "info-status" ) );
	return false;
}
// Send request for enddate push panel
function taskChangeEnddate ( taskId )
{
	ajaxGetRequestToElement( "./requests/taskquery.php?cmd=panel_changeenddate&id="+taskId, document.getElementById( "info-duedate" ) );
	return false;
}

function ajaxGetRequestToElement ( request, element )
{
	var m_tempContent = element;
	var ajaxRequest = new XMLHttpRequest();
	ajaxRequest.onreadystatechange = function() {
		if ( ajaxRequest.readyState == 4 && ajaxRequest.status == 200 ) {
			m_tempContent.innerHTML = ajaxRequest.responseText;
		}
	}
	ajaxRequest.open( "GET", request, true );
	ajaxRequest.send();
}

// Update task
function taskSendChangeStatus ( taskId, newStatus )
{
	ajaxRequestSubMainContentPost( "./requests/taskquery.php", "cmd=status&id="+taskId+"&status="+newStatus );
	return false;
}
function taskSendChangeDescription ( taskId )
{
	var m_description = encodeURIComponent(document.getElementById("input-description").value);
	ajaxRequestSubMainContentPost( "./requests/taskquery.php", "cmd=description&id="+taskId+"&description="+m_description );
	return false;
}
function taskSendChangeEnddate ( taskId )
{
	ajaxRequestSubMainContentPost( "./requests/taskquery.php", "cmd=enddate&id="+taskId+"&enddate="+
		((new Date(document.getElementById("input-date").value)).getTime()/1000 + (12 * 60 * 60)) );
	return false;
}
