<?php
function override_area_hours($area) {
    global $morningstarts, $morningstarts_minutes, $eveningends, $eveningends_minutes;
    global $day, $month, $year;
    global $memcache;

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

function get_user_groups() {
    $groups = array();
    if (isset($_SESSION['HTTP_PYORK_USER'])) {
        $groups[] = 'PYORK_USER:' . $_SESSION['HTTP_PYORK_USER'];
    }
    if (isset($_SESSION['HTTP_PYORK_TYPE'])) {
        $groups[] = $_SESSION['HTTP_PYORK_TYPE'];
    }
    $groups[] = 'REMOTE_ADDR:' . $_SERVER['REMOTE_ADDR'];
    
    $ils_user = isset($_SESSION['ILS_USER']) ? $_SESSION['ILS_USER'] : null;
    if ($ils_user && is_object($ils_user) && isset($ils_user->barcode)) {
        // got a valid ILS user 
        $groups[] = 'ILS_BARCODE:' . $ils_user->barcode;
        $groups[] = 'ILS_PROFILE:' . $ils_user->profile;
        $groups[] = 'ILS_CAT1:' . $ils_user->cat1;
        $groups[] = 'ILS_CAT2:' . $ils_user->cat2;
        $groups[] = 'ILS_CAT3:' . $ils_user->cat3;
        $groups[] = 'ILS_CAT4:' . $ils_user->cat4;
        $groups[] = 'ILS_CAT5:' . $ils_user->cat5;
        $groups[] = 'ILS_LIBRARY:' . $ils_user->library;
        $groups[] = 'ILS_STATUS:' . $ils_user->status;
    }
    return $groups;
}

function get_user_cyin() {
    return isset($_SESSION['HTTP_PYORK_CYIN']) ? $_SESSION['HTTP_PYORK_CYIN'] : null;  
}

function get_ils_user($cyin) {
    global $auth;
    global $memcache, $memcache_expiry;
    $ils_user = null;
    if (isset($auth['ils_user_api_url']) && !empty($auth['ils_user_api_url']) && $auth['ils_user_api_url'] != null) {
        if (strlen($cyin) == 9) {
            $user_api_url = $auth['ils_user_api_url'] . $cyin;
            $cache_key = 'mrbs_' . md5($user_api_url);
            if ($memcache) {
                $result = $memcache->get($cache_key);
                if ($result !== false) {
                    return $result;
                }
            }
            $json = @file_get_contents($user_api_url);
            $ils_user = json_decode($json);
            if ($ils_user && is_object($ils_user) && isset($ils_user->barcode)) {
                // got a valid ILS user object, cache it if memcache is configured
                if ($memcache) {
                    $memcache->set($cache_key, $ils_user, 0, $memcache_expiry);
                }
                return $ils_user;
            } else {
                // not a valid ILS user object
                return null;
            }
        }
    }
    return $ils_user;
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
    require_once "mrbs_sql.inc";
    
    global $morningstarts, $morningstarts_minutes, $eveningends, $eveningends_minutes, $resolution, $area_is_closed;

    // none available if area is closed
    if ($area_is_closed) {
        return 0;
    }
    
    $today_date = mktime(0,0,0);
    $given_date = mktime(0,0,0,$month,$day,$year);
    
    // none available if date is in the past
    if ($given_date < $today_date) {
        return 0;
    }
    
    // calculate next time slot
    $next_slot_timestamp = time();
    if ($given_date > $today_date) {
        $next_slot_timestamp = mktime($morningstarts, $morningstarts_minutes,0,$month,$day,$year);
    }
    $next_slot = floor( ($next_slot_timestamp + $resolution) / $resolution ) * $resolution;
    
    // calculate the last time slot
    $last_slot_starts = mktime($eveningends, $eveningends_minutes);
    if ($given_date > $today_date) {
        $last_slot_starts = mktime($eveningends, $eveningends_minutes,0,$month,$day,$year);
    }

    // check availability of each slot
    $available_slots = 0;
    $booking = array('room_id'=>$room);
    while ($next_slot <= $last_slot_starts) {
        $booking['start_time'] = $next_slot;
        $booking['end_time'] = $next_slot + $resolution;
        $booking['start_time_rfc2822'] = date(DATE_RFC2822, $booking['start_time']);
        $booking['end_time_rfc2822'] = date(DATE_RFC2822, $booking['end_time']);
        $booked = mrbsCheckFree($booking, 0 , 0);
        if (!$booked) {
            $available_slots++;
        }
        $next_slot += $resolution;
    }
    return $available_slots;
}

function check_area_availability($area, $year, $month, $day) {
    $availability = 0;
    $res = sql_query("SELECT * FROM mrbs_room WHERE area_id=$area ORDER BY sort_key");
    if ($res) {
        $count = sql_count($res);
        for ($i = 0; $i < $count; $i++) {
            $room = sql_row_keyed($res, $i);
            if (check_room_availability($room['id'], $year, $month, $day) > 0) {
                $availability++;
            }
        }
    }
    return $availability;
}

function setup_memcache() {
    global $memcache, $memcache_host, $memcache_port, $memcache_connection_timeout;
    
    // Setup memcached interface
    $memcache = false;
    if (isset($memcache_host) && isset($memcache_port) && isset($memcache_connection_timeout)) {
        $memcache = new Memcache();
        if (!@$memcache->pconnect($memcache_host, $memcache_port, $memcache_connection_timeout)) {
            $memcache = false;
        }
    }
}

function set_default_language() {
    global $default_language_tokens, $available_languages;
    // take language code from $_REQUEST or $_SESSION
    if (isset($_REQUEST['mylang']) && array_key_exists($_REQUEST['mylang'], $available_languages)) {
    	$default_language_tokens = $_REQUEST['mylang'];
    } else if (isset($_SESSION['mylang']) && array_key_exists($_SESSION['mylang'], $available_languages)) {
    	$default_language_tokens = $_SESSION['mylang'];
    }
    $_SESSION['mylang'] = $default_language_tokens;
}

function get_booking_rules() {
    global $default_language_tokens;
    // try finding rules matching language
    $res = sql_query("SELECT * FROM mrbs_rules WHERE lang='$default_language_tokens' ORDER BY id DESC");
    if ($res && sql_count($res) > 0) {
        $row = sql_row_keyed($res, 0);
        $html = $row['html'];
        if (strlen(trim($html)) > 0) {
            return $html;
        }
    }
    // try finding rules in any language
    $res = sql_query("SELECT * FROM mrbs_rules ORDER BY id DESC");
    if ($res && sql_count($res) > 0) {
        $row = sql_row_keyed($res, 0);
        $html = $row['html'];
        if (strlen(trim($html)) > 0) {
            return $html;
        }
    }
    // give up
    return null;
}

function sync_admins() {
    global $auth;
    $admins_in_config = array_unique($auth['admin']);
    $admins_in_db = array();
    $res = sql_query("SELECT DISTINCT username FROM mrbs_admins");
    if ($res) {
        $count = sql_count($res);
        for ($i = 0; $i < $count; $i++) {
            $row = sql_row_keyed($res, $i);
            $admins_in_db[] = $row['username'];
        }
    }
    $add_to_db = array_diff($admins_in_config, $admins_in_db);
    foreach ($add_to_db as $username) {
        $sql = 'INSERT INTO mrbs_admins(username) '
            . ' VALUES('
            . "'" . sql_escape($username) . "'"
            . ')';
        sql_command($sql);
    }
}
?>
