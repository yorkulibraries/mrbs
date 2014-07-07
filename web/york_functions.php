<?php
function override_area_hours($area) {
	global $morningstarts, $morningstarts_minutes, $eveningends, $eveningends_minutes;
	global $day, $month, $year;
  
  // calculate selected day of the week
  if (!$day || !$month || !$year) {
	  $dow = date('N', mktime(0,0,0,date('n'),date('j'),date('Y')));
	} else {
		$dow = date('N', mktime(0,0,0,$month,$day,$year));
	}

	// get the hours the selected day of the week            
  $sql = "SELECT * FROM mrbs_area_hours WHERE area_id=$area AND dayoftheweek=$dow LIMIT 1";
  $res = sql_query($sql);
  if ($res) {
	  $row = sql_row_keyed($res, 0);
	  $morningstarts = $row['morningstarts'];
	  $morningstarts_minutes = $row['morningstarts_minutes'];
		$eveningends = $row['eveningends'];
		$eveningends_minutes = $row['eveningends_minutes'];
	}
}
?>
