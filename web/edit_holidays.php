<?php
require_once "defaultincludes.inc";

// Check the user is authorised for this page
checkAuthorised();

// Also need to know whether they have admin rights
$user = getUserName();
$required_level = (isset($max_level) ? $max_level : 2);
$is_admin = (authGetUserLevel($user) >= $required_level);

$error = null;
$success = null;

if (isset($_POST['date']) && preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $_POST['date'])) {
  $sql = 'INSERT INTO mrbs_closed_dates(closed_date) '
    . ' VALUES('
    . "'" . $_POST['date'] . "'"
    . ')';

  if (sql_command($sql) < 0) {
    $error = 'Error adding a holiday. ' . sql_error();
  } else {
    $success = 'Holiday added successfully.';
  }
}

if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
  $sql = 'DELETE FROM mrbs_closed_dates WHERE id=' . $_REQUEST['delete'];
  if (sql_command($sql) < 0) {
    $error = 'Error deleting a holiday. ' . sql_error();
  } else {
    $success = 'Holiday deleted successfully.';
  }
}

$rows = array();
$sql = "SELECT * FROM mrbs_closed_dates ORDER BY closed_date DESC LIMIT 100";
$res = sql_query($sql);
if ($res) {
  $count = sql_count($res);
  for ($i = 0; $i < $count; $i++) {
    $rows[] = sql_row_keyed($res, $i);
  }
}

?>

<?php print_header($day, $month, $year, isset($area) ? $area : "", isset($room) ? $room : ""); ?>

<h2>Holidays (closed dates)</h2>

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

<form class="form-inline" role="form" action="edit_holidays.php" method="post">
  <div class="form-group">
    <label class="sr-only" for="date">Date</label>
    <input name="date" type="date" class="form-control" id="date" placeholder="Date (YYYY-MM-DD)">
  </div>
  <button type="submit" class="btn btn-primary" title="Add">Add</button>
</form>

<h2>Recent holidays (closed dates)</h2>
<ul class="list-group">
  <?php foreach($rows as $row) { ?>
    <li class="list-group-item"><?php echo $row['closed_date']; ?> <a title="Delete" class="btn btn-danger btn-sm" href="edit_holidays.php?delete=<?php echo $row['id']; ?>"> <span class="glyphicon glyphicon-trash"></span></a></li>
  <?php } ?>
</ul>

<?php output_trailer(); ?>
