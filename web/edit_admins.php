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

if (!empty($_POST['admin'])) {
  $sql = 'INSERT INTO mrbs_admins(username) '
    . ' VALUES('
    . "'" . sql_escape($_POST['admin']) . "'"
    . ')';

  if (sql_command($sql) < 0) {
    $error = 'Error adding an administrator. ' . sql_error();
  } else {
    $success = 'Administrator added successfully.';
  }
}

if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
  $sql = 'DELETE FROM mrbs_admins WHERE id=' . $_REQUEST['delete'];
  if (sql_command($sql) < 0) {
    $error = 'Error deleting an administrator. ' . sql_error();
  } else {
    $success = 'Administrator deleted successfully.';
  }
}

$rows = array();
$sql = "SELECT * FROM mrbs_admins";
$res = sql_query($sql);
if ($res) {
  $count = sql_count($res);
  for ($i = 0; $i < $count; $i++) {
    $rows[] = sql_row_keyed($res, $i);
  }
}

?>

<?php print_header($day, $month, $year, isset($area) ? $area : "", isset($room) ? $room : ""); ?>

<h2><?php echo get_vocab('administrators'); ?></h2>

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

<form class="form-inline" role="form" action="edit_admins.php" method="post">
  <div class="form-group">
    <label class="sr-only" for="admin">Username</label>
    <input name="admin" type="text" class="form-control" id="admin" placeholder="Enter username">
  </div>
  <button type="submit" class="btn btn-primary" title="Add">Add</button>
</form>
<br>
<ul class="list-group">
  <?php foreach($rows as $row) { ?>
    <li class="list-group-item"><?php echo $row['username']; ?> <a title="Delete" class="btn btn-danger btn-sm" href="edit_admins.php?delete=<?php echo $row['id']; ?>"> <span class="glyphicon glyphicon-trash"></span></a></li>
  <?php } ?>
</ul>

<?php output_trailer(); ?>
