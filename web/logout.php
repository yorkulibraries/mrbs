<?php
require "defaultincludes.inc";

ob_start();
unset($_SESSION['REMOTE_USER']);
setcookie('pyauth', '', time()-(3600 * 24 * 365), '/', 'yorku.ca');
setcookie('mayaauth', '', time()-(3600 * 24 * 365), '/', 'yorku.ca');
?>
<?php print_header($day, $month, $year, $area, isset($room) ? $room : ""); ?>
<h1><?php echo get_vocab("logged_out")?></h1>
<div class="alert alert-successs" role="alert">
  <?php echo get_vocab("logged_out_success")?>
</div>
<?php print_footer(TRUE); ?>
