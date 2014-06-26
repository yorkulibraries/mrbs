<?php 
/**
 * Edit Area Hours is a customization from YorkU Libraries 
 * to be able to set area hours based on day of the week.
 *
**/ 
require_once "defaultincludes.inc";
require_once "mrbs_sql.inc";
// Check the user is authorised for this page
checkAuthorised();
// Also need to know whether they have admin rights
$user = getUserName();
$required_level = (isset($max_level) ? $max_level : 2);
$is_admin = (authGetUserLevel($user) >= $required_level);

print_header($day, $month, $year, isset($area) ? $area : "", isset($room) ? $room : "");

// Get the details for this area
  $res = sql_query("SELECT * FROM $tbl_area WHERE id=$area LIMIT 1");
  if (! $res)
  {
    fatal_error(0, get_vocab("error_area") . $area . get_vocab("not_found"));
  }
  $row = sql_row_keyed($res, 0);
  sql_free($res);

// Check to see if this area has hours. If not, generate default values.
  $res = sql_query("SELECT * FROM mrbs_area_hours WHERE area_id=$area LIMIT 1");
  if (! $res)
  {
    fatal_error(0, get_vocab("error_area") . $area . get_vocab("not_found"));
  }
  if (sql_row_keyed($res) == 0) {
    echo "<h3> Generating default values for new area! </h3>\n";
    for($i=1; $i<8; $i++) {

        $sql = "INSERT INTO mrbs_area_hours ". 
               " (area_id, morningstarts, morningstarts_minutes, eveningends, eveningends_minutes, dayoftheweek) " .
               " VALUES(" . $area ." ,8,0,21,0,". $i .")";

        if (sql_command($sql) < 0) {
          echo get_vocab("insert_hours_failed") . "<br>\n";
          trigger_error(sql_error(), E_USER_WARNING);
          fatal_error(FALSE, get_vocab("fatal_db_error"));
        }
    }
  }
  sql_free($res);
?>
<div id="">
  <?php 
    if ($_GET['status'] == 'success') {
     echo "<h3 style='color: green;'>Area hours have been successfully updated!</h3>\n";	
    } 
    if ($_GET['status'] == 'error') {
     echo "<h3 style='color: red;'>An error occured while updating the hours.System Admin has been notified.</h3>\n";
    }
  ?>
  <form id="mrbs_area_hours_form" action="edit_area_hours_handler.php" method="post" class="form_general">
    <input type="hidden" name="area" value="<?php echo $row["id"]?>">
    <fieldset>
      <legend>MRBS AREA HOURS : <?php echo $row["area_name"]; ?></legend>
      <table>
        <tr>
         <th>Day</th>
         <th>Start Hour</th>
         <th>Start Minute</th>
         <th>Evening Hour</th>
         <th>Evening Minute</th>
        </tr>
        <tr><td colspan="5"><hr></td></tr>
        <?php
          // Loop through all of the days in the week and display the hours from the database
	  for ($day=1; $day<8; $day++) {
            
           $sql = "SELECT * FROM mrbs_area_hours WHERE area_id=$area AND dayoftheweek=$day order by dayoftheweek limit 1";
          
           $result = sql_query($sql);

           if (!$result) {
            echo "Could not successfully run query ($sql) from DB: " . mysql_error();
            exit;
           }
	   $hours = sql_row_keyed($result, 0);

	  // if (mysql_num_rows($result) == 0) {
           // echo "One of the day of the weeks is not set. Please contact your system admin.";
           // exit;
          // }
	   // Get the MRBS HOURS Row if there is one.
          // $hours = mysql_fetch_assoc($result)
        ?> 
        <tr>
         <td>
	<?php
	  switch($hours['dayoftheweek']) {

            case 1: 
                 echo "<input type='hidden' name='dayoftheweek_$hours[dayoftheweek]' value=1 readonly=readonly /> MONDAY\n";
                 break;
            case 2: 
                 echo "<input type='hidden' name='dayoftheweek_$hours[dayoftheweek]' value=2 readonly=readonly /> TUESDAY\n";
                 break;
            case 3: 
                 echo "<input type='hidden' name='dayoftheweek_$hours[dayoftheweek]' value=3 readonly=readonly /> WEDNESDAY\n";
                 break;
            case 4: 
                 echo "<input type='hidden' name='dayoftheweek_$hours[dayoftheweek]' value=4 readonly=readonly /> THURSDAY\n";
                 break;
            case 5: 
                 echo "<input type='hidden' name='dayoftheweek_$hours[dayoftheweek]' value=5 readonly=readonly /> FRIDAY\n";
                 break;
            case 6: 
                 echo "<input type='hidden' name='dayoftheweek_$hours[dayoftheweek]' value=6 readonly=readonly /> SATURDAY\n";
                 break;
            case 7: 
                 echo "<input type='hidden' name='dayoftheweek_$hours[dayoftheweek]' value=7 readonly=readonly/> SUNDAY\n";
                 break;
          }
        ?>
         </td>
         <td>
          <select name="start_hour_select_<?php echo $hours['dayoftheweek']; ?>">
           <?php 
	      for($i=0; $i<24; $i++) {
		echo "<option value='$i'"; 
		  if ($hours['morningstarts'] == $i) { echo "selected=selected"; }
                echo ">$i</option>\n";
	      }
	   ?>
           </select>
         </td>
         <td>
          <select name="start_minutes_select_<?php echo $hours['dayoftheweek']; ?>">
           <?php 
              for($i=0; $i<50; $i=$i+15) {
                echo "<option value='$i'"; 
                  if ($hours['morningstarts_minutes'] == $i) { echo "selected=selected"; }
                echo ">$i</option>\n";
              }
           ?>
           </select>
         </td>
         <td>
          <select name="evening_hour_select_<?php echo $hours['dayoftheweek']; ?>">
           <?php
              for($i=0; $i<24; $i++) {
                echo "<option value='$i'";
                  if ($hours['eveningends'] == $i) { echo "selected=selected"; }
                echo ">$i</option>\n";
              }
           ?>
           </select>
         </td>
         <td>
          <select name="evening_minutes_select_<?php echo $hours['dayoftheweek']; ?>">
           <?php
              for($i=0; $i<50; $i=$i+15) {
                echo "<option value='$i'";
                  if ($hours['eveningends_minutes'] == $i) { echo "selected=selected"; }
                echo ">$i</option>\n";
              }
           ?>
           </select>
         </td>
	 <td><input type="hidden" name="hours_id_<?php echo $hours['dayoftheweek']; ?>" value="<?php echo $hours['id']; ?>" /></td>
        </tr>
        <?php } 
        mysql_free_result($result); 
        ?>
      <tr><td><br/><input type="submit" name="submit_hours" value="Submit" style="margin-left:0em;font-size: 14px;" /></td></tr>
      </table>
    </fieldset>
  </form>
</div>
