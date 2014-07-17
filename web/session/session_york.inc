<?php 
function authGet()
{
  // User is expected to already be authenticated by the web server, so do nothing
}

function getUserName()
{
  if ((!isset($_SERVER['REMOTE_USER'])) ||
      (!is_string($_SERVER['REMOTE_USER'])) ||
      (empty($_SERVER['REMOTE_USER'])))
  {
		if ((isset($_SESSION['REMOTE_USER'])) &&
	      (is_string($_SESSION['REMOTE_USER'])) &&
	      (!empty($_SESSION['REMOTE_USER'])))
	  {
			return $_SESSION['REMOTE_USER'];
		}
    return NULL;
  } 
  else
  {
	  $_SESSION['REMOTE_USER'] = $_SERVER['REMOTE_USER'];
	  foreach ($_SERVER as $key => $val) {
	    if (strpos($key, 'HTTP_PYORK_') !== false) {
	      $_SESSION[$key] = $val;
	    }
	  }
    return $_SERVER['REMOTE_USER'];
  }
}

function PrintLogonBox()
{
	// User is expected to already be authenticated by the web server, so do nothing
}

