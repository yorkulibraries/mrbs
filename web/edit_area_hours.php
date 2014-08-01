<?php
require_once "defaultincludes.inc";
require_once "mrbs_sql.inc";

// Check the user is authorised for this page
checkAuthorised();

// Also need to know whether they have admin rights
$user = getUserName();
$required_level = (isset($max_level) ? $max_level : 2);
$is_admin = (authGetUserLevel($user) >= $required_level);

print_header($day, $month, $year, isset($area) ? $area : "", isset($room) ? $room : "");

$month = ($_REQUEST['month'] > 0 && $_REQUEST['month'] <= 12) ? $_REQUEST['month'] : 0;

$error = null;
$success = null;
if (isset($_POST['morningstarts'])) {
  for ($i = 0; $i < 7; $i++) {
    if ($_POST['id'][$i]) {
      $sql = 'UPDATE mrbs_area_hours SET '
        . 'morningstarts=' . $_POST['morningstarts'][$i] . ', morningstarts_minutes=' . $_POST['morningstarts_minutes'][$i] . ',' 
        . 'eveningends=' . $_POST['eveningends'][$i] . ', eveningends_minutes=' . $_POST['eveningends_minutes'][$i] . ','
        . 'dayoftheweek=' . $_POST['dayoftheweek'][$i]
        . ' WHERE month=' . $month 
        . ' AND id=' . $_POST['id'][$i];
    } else {
      $sql = 'INSERT INTO mrbs_area_hours(morningstarts,morningstarts_minutes,eveningends,eveningends_minutes,dayoftheweek,month) '
        . ' VALUES('
        . $_POST['morningstarts'][$i] . ',' . $_POST['morningstarts_minutes'][$i] . ',' 
        . $_POST['eveningends'][$i] . ',' . $_POST['eveningends_minutes'][$i] . ','
        . $_POST['dayoftheweek'][$i] . ',' . $month
        . ')';
    }

    if (sql_command($sql) < 0) {
      $error = 'Error updating hours. ' . sql_error();
    } else {
      $success = 'Hours updated successfully.';
    }
  }
}

$rows = array();
$sql = "SELECT * FROM mrbs_area_hours WHERE month=$month";
$res = sql_query($sql);
if ($res) {
  $count = sql_count($res);
  for ($i = 0; $i < $count; $i++) {
    $rows[] = sql_row_keyed($res, $i);
  }
}
?>

<h2>Area Hours</h2>

<?php if ($error) { ?>
  <div class="alert alert-danger" role="alert">
    <?php echo $error; ?>
  </div>
<?php } ?>

<?php if ($success) { ?>
  <div class="alert alert-success" role="alert">
    <?php echo $success; ?>
  </div>
<?php } ?>

<p class="help-block">Set the hours for a specific month or set default hours for the whole year.</p>

<div class="btn-group">
  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
    <?php echo $month == 0 ? 'Default' : date('F', mktime(0, 0, 0, $month, 10)); ?> <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <li><a href="edit_area_hours.php?month=0">Default</a></li>
    <li class="divider"></li>
    <?php for ($i = 1; $i <= 12; $i++) { ?>
      <li><a href="edit_area_hours.php?month=<?php echo $i; ?>"><?php echo date("F", mktime(0, 0, 0, $i, 10)); ?></a></li>
    <?php } ?>
  </ul>
</div>

<form role="form" action="edit_area_hours.php" method="post">
  <input type="hidden" name="month" value="<?php echo $month; ?>" />
  
  <div class="col-xs-2 col-sm-1"></div>
  <div class="help-block col-xs-4 col-sm-2">First bookable time slot</div>
  <div class="help-block col-xs-6 col-sm-9">Last bookable time slot</div>
  
  <?php foreach (array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday') as $i=>$weekday) { ?>
    <input type="hidden" name="id[]" value="<?php echo $rows[$i]['id']; ?>">
    <input type="hidden" name="dayoftheweek[]" value="<?php echo $i; ?>">
    
    <div class="form-group">
      <div class="col-xs-2 col-sm-1"><?php echo $weekday; ?></div>
      
      <label for="morningstarts<?php echo $i; ?>" class="sr-only">First Slot Hour</label>
      <div class="col-xs-2 col-sm-1">
        <select id="morningstarts<?php echo $i; ?>" name="morningstarts[]">
        <option <?php if (-1 == $rows[$i]['morningstarts']) { echo 'selected="selected"'; } ?> value="-1">Closed</option>
        <?php for ($h = 0; $h < 24; $h++) { ?>
          <option <?php if ($h == $rows[$i]['morningstarts']) { echo 'selected="selected"'; } ?> value="<?php echo $h; ?>"><?php echo $h; ?></option>
        <?php } ?>
        </select>
      </div>
      
      <label for="morningstarts_minutes<?php echo $i; ?>" class="sr-only">First Slot Minute</label>
      <div class="col-xs-2 col-sm-1">
        <select id="morningstarts_minutes<?php echo $i; ?>" name="morningstarts_minutes[]">
          <option <?php if (0 == $rows[$i]['morningstarts_minutes']) { echo 'selected="selected"'; } ?> value="0">0</option>
          <option <?php if (30 == $rows[$i]['morningstarts_minutes']) { echo 'selected="selected"'; } ?> value="30">30</option>
        </select>
      </div>
      
      <label for="eveningends<?php echo $i; ?>" class="sr-only">Last Slot Hour</label>
      <div class="col-xs-2 col-sm-1">
        <select id="eveningends<?php echo $i; ?>" name="eveningends[]">
        <?php for ($h = 0; $h < 24; $h++) { ?>
          <option <?php if ($h == $rows[$i]['eveningends']) { echo 'selected="selected"'; } ?> value="<?php echo $h; ?>"><?php echo $h; ?></option>
        <?php } ?>
        </select>
      </div>
      
      <label for="eveningends_minutes<?php echo $i; ?>" class="sr-only">Last Slot Minute</label>
      <div class="col-xs-4 col-sm-8">
        <select id="eveningends_minutes<?php echo $i; ?>" name="eveningends_minutes[]">
          <option <?php if (0 == $rows[$i]['eveningends_minutes']) { echo 'selected="selected"'; } ?> value="0">0</option>
          <option <?php if (30 == $rows[$i]['eveningends_minutes']) { echo 'selected="selected"'; } ?> value="30">30</option>
        </select>
      </div>
    </div>
  <?php } ?>
  
  <div class="btn-group">
    <button type="submit" class="btn btn-default" title="Save">Save</button>
  </div>
</form>

<?php output_trailer(); ?>
