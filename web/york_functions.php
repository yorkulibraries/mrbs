<?php
function override_area_hours($area) {
	global $morningstarts, $morningstarts_minutes, $eveningends, $eveningends_minutes, $resolution;
	global $day, $month, $year;
  
 	// calculate selected day of the week
  if (!$day || !$month || !$year) {
	  $dow = date('N', mktime(0,0,0,date('n'),date('j'),date('Y')));
	} else {
		$dow = date('N', mktime(0,0,0,$month,$day,$year));
	}

  if (strrpos($_SERVER['REQUEST_URI'], 'week.php') !== false) {
		$sql = "select min(morningstarts) as morningstarts, max(eveningends) as eveningends from mrbs_area_hours where morningstarts>0  and area_id=$area";
		$res = sql_query($sql);
	  if ($res) {
		  $row = sql_row_keyed($res, 0);
		  $morningstarts = $row['morningstarts'];
		  $morningstarts_minutes = 0;
			$eveningends = $row['eveningends'];
			$eveningends_minutes = 59;
		}
	} else {
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
		if ($morningstarts == 0 && $morningstarts_minutes == 0 
		    && $eveningends == 0 && $eveningends_minutes == 0) {
			$resolution = 1000000 * SECONDS_PER_DAY;
			global $area_is_closed;
			$area_is_closed = true;
		}
	}
}

function get_user_group() {
    return isset($_SESSION['HTTP_PYORK_TYPE']) ? $_SESSION['HTTP_PYORK_TYPE'] : null;  
}

function get_area_name($room) {
    $sql = "select area_name from mrbs_room r, mrbs_area a where r.area_id=a.id and r.id=$room";
	$res = sql_query($sql);
	if ($res) {
	    $row = sql_row_keyed($res, 0);
	    return $row['area_name'];
    }
    return null;
}
?>
