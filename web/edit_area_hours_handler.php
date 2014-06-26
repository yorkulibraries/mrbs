<?php
/** 
 * Form Handler for Edit Area Hours. 
 * Updates hours for the specific area on form submission
 *
 */
require_once "defaultincludes.inc";
require_once "mrbs_sql.inc";

if($_POST['submit_hours'] == 'Submit') {
  for($i=1; $i<8; $i++) {
	$sql = "UPDATE mrbs_area_hours SET " .
               " morningstarts=". $_POST["start_hour_select_$i"] . 
               " ,morningstarts_minutes=" . $_POST["start_minutes_select_$i"] . 
               " ,eveningends=". $_POST["evening_hour_select_$i"]  .
               " ,eveningends_minutes=". $_POST["evening_minutes_select_$i"]  .
               " ,dayoftheweek=". $_POST["dayoftheweek_$i"]  .
               " WHERE id=". $_POST["hours_id_$i"] . " AND area_id=". $_POST["area"];

 	if (sql_command($sql) < 0)
        {
          echo get_vocab("update_hours_failed") . "<br>\n";
          trigger_error(sql_error(), E_USER_WARNING);
          fatal_error(FALSE, get_vocab("fatal_db_error"));
        }

  }
	//On successful update, take user back to mrbs_area_hours page
	Header("Location: edit_area_hours.php?status=success&area=" . $_POST["area"]);
}else {
	// Something went wrong!
	$message = "MRBS AREA HOURS ERROR OCCURED! \n";
        foreach($_POST as $key => $value) {
            $message .= "$key => $value \n";
        } 
	$message .= "\n\n";
	mail("sadaqain@yorku.ca", "MRBS AREA HOURS ERROR", $message);
	Header("Location: edit_area_hours.php?status=error&area=". $_POST["area"]);
}

?>
