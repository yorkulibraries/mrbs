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

function get_user_groups() {
    $groups = array();
    if (isset($_SESSION['HTTP_PYORK_USER'])) {
        $groups[] = 'PYORK_USER:' . $_SESSION['HTTP_PYORK_USER'];
    }
    if (isset($_SESSION['HTTP_PYORK_TYPE'])) {
        $groups[] = $_SESSION['HTTP_PYORK_TYPE'];
    }
    return $groups;
}

function get_user_cyin() {
    return isset($_SESSION['HTTP_PYORK_CYIN']) ? $_SESSION['HTTP_PYORK_CYIN'] : null;  
}

function get_user_categories($cat) {
    global $auth;
    
    $categories = array();
    $cyin = get_user_cyin();
    if (strlen($cyin) == 9) {
        $jsonurl = $auth['ils_user_api_url'] . 
        $json = @file_get_contents($auth['ils_user_api_url'] . $cyin);
        $ils_user = json_decode($json);
    }
    return $categories;
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

override_area_hours($area);

?>
