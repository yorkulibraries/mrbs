<?php 
function authGet()
{
  // User is expected to already be authenticated by the web server, so do nothing
}

function getUserName()
{  
  $username = null;
  
  if ((!isset($_SERVER['REMOTE_USER'])) ||
      (!is_string($_SERVER['REMOTE_USER'])) ||
      (empty($_SERVER['REMOTE_USER'])))
  {
		if ((isset($_SESSION['REMOTE_USER'])) &&
	      (is_string($_SESSION['REMOTE_USER'])) &&
	      (!empty($_SESSION['REMOTE_USER'])))
	  {
			$username = $_SESSION['REMOTE_USER'];
		}
  } 
  else
  {
	  $_SESSION['REMOTE_USER'] = $_SERVER['REMOTE_USER'];
	  foreach ($_SERVER as $key => $val) {
	    if (strpos($key, 'HTTP_PYORK_') !== false) {
	      $_SESSION[$key] = $val;
	    }
	  }
	  $ils_user = get_ils_user(get_user_cyin());
	  if ($ils_user && is_object($ils_user) && isset($ils_user->barcode)) {
	    $_SESSION['ILS_USER'] = $ils_user;
    }
    $username = $_SERVER['REMOTE_USER'];
  }

  return $username;
}

function PrintLogonBox()
{
	// User is expected to already be authenticated by the web server, so do nothing
}

