<?php
ob_start();
session_destroy();
setcookie('pyauth', '', time()-(3600 * 24 * 365), '/', 'yorku.ca');
setcookie('mayaauth', '', time()-(3600 * 24 * 365), '/', 'yorku.ca');
header('Location: index.php');
exit;
?>
