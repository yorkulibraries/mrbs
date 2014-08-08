<?php
function override_area_hours($area) {
    global $morningstarts, $morningstarts_minutes, $eveningends, $eveningends_minutes;
    global $day, $month, $year;
  
     // calculate selected day of the week
    if (!$day || !$month || !$year) {
        $dow = date('N');
    } else {
        $dow = date('N', mktime(0,0,0,$month,$day,$year));
    }
    
    // check if area is closed on the given date
    $date = date('Y-m-d', mktime(0,0,0,$month,$day,$year));
    $sql = "select * from mrbs_closed_dates where closed_date='$date' LIMIT 1";
    $res = sql_query($sql);
    if (sql_count($res) > 0) {
      area_closed();
      return;
    }

    if (strrpos($_SERVER['REQUEST_URI'], 'week.php') !== false) {
        $sql = "select min(morningstarts) as morningstarts, max(eveningends) as eveningends from mrbs_area_hours where morningstarts>0";
        $res = sql_query($sql);
        if ($res) {
            $row = sql_row_keyed($res, 0);
            $morningstarts = $row['morningstarts'];
            $morningstarts_minutes = 0;
            $eveningends = $row['eveningends'];
            $eveningends_minutes = 59;
        }
    } else {
        // get the hours for the selected day of the week 
        $sql = "SELECT * FROM mrbs_area_hours WHERE dayoftheweek=$dow AND (month=$month OR month=0) ORDER BY month DESC LIMIT 1";
        $res = sql_query($sql);
        if ($res) {
            $row = sql_row_keyed($res, 0);
            $morningstarts = $row['morningstarts'];
            $morningstarts_minutes = $row['morningstarts_minutes'];
            $eveningends = $row['eveningends'];
            $eveningends_minutes = $row['eveningends_minutes'];
            
            // override the hours in the area table, too.
            $sql = "UPDATE mrbs_area SET "
                . " morningstarts=$morningstarts,morningstarts_minutes=$morningstarts_minutes, "
                . " eveningends=$eveningends,eveningends_minutes=$eveningends_minutes";
                
            if (sql_command($sql) < 0) {
                // FIXME: must deal with this more gracefully
                die(sql_error());
            }
        }
        if ($morningstarts == -1) {
            area_closed();
        }
    }
}

function area_closed() {
  global $resolution, $area_is_closed;
  
  $resolution = 1000000 * SECONDS_PER_DAY;
  $area_is_closed = true;
}

function get_user_group() {
    return isset($_SESSION['HTTP_PYORK_TYPE']) ? $_SESSION['HTTP_PYORK_TYPE'] : null;  
}

function get_area_name($area) {
    $sql = "select area_name from mrbs_area where id=$area";
    $res = sql_query($sql);
    if ($res) {
        $row = sql_row_keyed($res, 0);
        return $row['area_name'];
    }
    return null;
}

function check_room_availability($room, $year, $month, $day) {
    return 1;
}

function check_area_availability($area, $year, $month, $day) {
    $availability = 0;
    $res = sql_query("SELECT * FROM mrbs_room WHERE area_id=$area ORDER BY sort_key");
    if ($res) {
        $count = sql_count($res);
        for ($i = 0; $i < $count; $i++) {
            $room = sql_row_keyed($res, $i);
            if (check_room_availability($room['id'])) {
                $availability++;
            }
        }
    }
    return $availability;
}
?>
