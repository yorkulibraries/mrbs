<?php
session_start();
if ((isset($_SERVER['REMOTE_USER'])) &&
    (is_string($_SERVER['REMOTE_USER'])) &&
    (!empty($_SERVER['REMOTE_USER'])))
{
	$_SESSION['REMOTE_USER'] = $_SERVER['REMOTE_USER'];
}
header('Location: index.php');
exit;
?>