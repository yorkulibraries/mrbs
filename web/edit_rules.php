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

$lang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : $default_language_tokens;

if (isset($_POST['html'])) {
    $sql = 'INSERT INTO mrbs_rules(lang, html) '
        . ' VALUES('
        . "'" . sql_escape($lang) . "'"
        . ",'" . sql_escape($_POST['html']) . "'"
        . ')';
        
    if (!empty($_REQUEST['id'])) {
        $sql = 'UPDATE mrbs_rules SET '
        . " html='" . sql_escape($_POST['html']) . "'"
        . " WHERE id=" . sql_escape($_REQUEST['id']);
    }

    if (sql_command($sql) < 0) {
        $error = 'Error updating rules ' . sql_error();
    } else {
        $success = 'Rules updated successfully.';
    }
}

$rules = null;
$res = sql_query("SELECT * FROM mrbs_rules WHERE lang='" . sql_escape($lang) . "' ORDER BY id DESC");
if ($res && sql_count($res) > 0) {
    $rules = sql_row_keyed($res, 0);
}

?>

<?php print_header($day, $month, $year, isset($area) ? $area : "", isset($room) ? $room : ""); ?>

<h2><?php echo get_vocab('booking_rules'); ?></h2>

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

<div class="btn-group">
  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
    <?php echo $available_languages[$lang]; ?> <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <?php foreach ($available_languages as $k=>$v) { ?>
      <li><a href="edit_rules.php?lang=<?php echo $k; ?>"><?php echo $v; ?></a></li>
    <?php } ?>
  </ul>
</div>

<form role="form" action="edit_rules.php" method="post">
  <input type="hidden" name="id" value="<?php if ($rules) echo $rules['id']; ?>">
  <input type="hidden" name="lang" value="<?php echo $lang; ?>">
  <div class="form-group">
    <label class="sr-only" for="html"><?php echo get_vocab('booking_rules'); ?></label>
    <textarea name="html" class="form-control" id="html" rows="20"><?php if ($rules) echo $rules['html']; ?></textarea>
  </div>
  <button type="submit" class="btn btn-primary" title="Save">Save</button>
</form>

<?php output_trailer(); ?>

<script src="js/bootstrap3-wysihtml5.all.min.js"></script>
<script>
  $('textarea').wysihtml5({
    toolbar: {
      fa: true
    }
  });
</script>
