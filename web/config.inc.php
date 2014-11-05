<?php

// $Id: config.inc.php 2799 2014-01-09 12:44:22Z cimorrison $

/**************************************************************************
 *   MRBS Configuration File
 *   Configure this file for your site.
 *   You shouldn't have to modify anything outside this file.
 *
 *   This file has already been populated with the minimum set of configuration
 *   variables that you will need to change to get your system up and running.
 *   If you want to change any of the other settings in systemdefaults.inc.php
 *   or areadefaults.inc.php, then copy the relevant lines into this file
 *   and edit them here.   This file will override the default settings and
 *   when you upgrade to a new version of MRBS the config file is preserved.
 **************************************************************************/

/**********
 * Timezone
 **********/
 
// The timezone your meeting rooms run in. It is especially important
// to set this if you're using PHP 5 on Linux. In this configuration
// if you don't, meetings in a different DST than you are currently
// in are offset by the DST offset incorrectly.
//
// Note that timezones can be set on a per-area basis, so strictly speaking this
// setting should be in areadefaults.inc.php, but as it is so important to set
// the right timezone it is included here.
//
// When upgrading an existing installation, this should be set to the
// timezone the web server runs in.  See the INSTALL document for more information.
//
// A list of valid timezones can be found at http://php.net/manual/timezones.php
// The following line must be uncommented by removing the '//' at the beginning
$timezone = "America/Toronto";


/*******************
 * Database settings
 ******************/
// Which database system: "pgsql"=PostgreSQL, "mysql"=MySQL,
// "mysqli"=MySQL via the mysqli PHP extension
$dbsys = "mysqli";
// Hostname of database server. For pgsql, can use "" instead of localhost
// to use Unix Domain Sockets instead of TCP/IP. For mysql/mysqli "localhost"
// tells the system to use Unix Domain Sockets, and $db_port will be ignored;
// if you want to force TCP connection you can use "127.0.0.1".
$db_host = "localhost";
// If you need to use a non standard port for the database connection you
// can uncomment the following line and specify the port number
// $db_port = 1234;
// Database name:
$db_database = "mrbs";
// Schema name.  This only applies to PostgreSQL and is only necessary if you have more
// than one schema in your database and also you are using the same MRBS table names in
// multiple schemas.
//$db_schema = "public";
// Database login user name:
$db_login = "mrbs";
// Database login password:
$db_password = "password";
// Prefix for table names.  This will allow multiple installations where only
// one database is available
$db_tbl_prefix = "mrbs_";
// Uncomment this to NOT use PHP persistent (pooled) database connections:
// $db_nopersist = 1;


/* Add lines from systemdefaults.inc.php and areadefaults.inc.php below here
   to change the default configuration. Do _NOT_ modify systemdefaults.inc.php
   or areadefaults.inc.php.  */

$theme = "york";

$auth["type"] = "york";

// How to get and keep the user ID.
// set to "york" for prod, "ip" for dev
$auth["session"] = "ip"; 

// must be logged in AND authorized to view 
$auth['deny_public_access'] = FALSE;

// The list of administrators (can modify other peoples settings).
// set to real PY username for prod and "127.0.0.1" for dev
unset($auth['admin']);
$auth['admin'] = array('::1');

// admin only restrictions
$auth['only_admin_can_book_repeat'] = TRUE;
$auth['only_admin_can_book_multiday'] = TRUE;
$auth['only_admin_can_select_multiroom'] = TRUE;

// types of users allowed to book, empty means ANYONE 
// Possible values are: 
// UNDERGRAD:STUDENT, GRAD:STUDENT, PYORK_USER:somepyorkusername
// ILS_CAT(1-5):SOMETHING, ILS_PROFILE:SOMETHING, REMOTE_ADDR:SOMETHING
$auth['allowed_user_groups'] = array(
    'UNDERGRAD:STUDENT',
    'NONDEG:STUDENT',
    'GRAD:STUDENT',
);

// ILS user API
$auth['ils_user_api_url'] = NULL;

// normal users cannot cancel bookings
$auth['users_cannot_delete'] = TRUE;

// Set a maximum duration for bookings
$max_duration_enabled = TRUE; // Set to TRUE if you want to enforce a maximum duration
$max_duration_secs = 60*60*3;  // (seconds) - when using "times"

$max_per_interval_global_enabled['day']    = TRUE;
$max_per_interval_global['day'] = 1;      // max 1 bookings per day in total

// language/locale
$available_languages = array('en'=>'English', 'fr'=>'French');
$default_language_tokens = 'en';

// don't change language based on browser setttings
$disable_automatic_language_changing = true;

// Trailer type.   FALSE gives a trailer complete with links to days, weeks and months before
// and after the current date.    TRUE gives a simpler trailer that just has links to the
// current day, week and month.
$simple_trailer = TRUE;

// set $url_base so links in email notifications will work
$url_base = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);

// Mail settings
$mail_settings['area_admin_on_bookings'] = FALSE;
$mail_settings['details'] = TRUE;
$mail_settings['from'] = 'no-reply@yorku.ca';
$mail_settings['recipients'] = '';

// Memcached
$memcache_host='localhost';
$memcache_port=11211;
$memcache_connection_timeout=1;
$memcache_expiry=300;

// global nav menu
$global_nav_links = array(
    'libraries_home' => '//www.library.yorku.ca/',
    'libraries_with_study_rooms' => array(
        'Adaptive Equipment Rooms' => '/rooms/las/',
        'Leslie Frost Library (Glendon Campus)' => '/rooms/frost/',
        'Peter F. Bronfman Business Library' => '/rooms/bronfman/',
        'Scott Library - 1st floor' => '/rooms/scott/',
        'Scott Library - 2nd floor' => '/rooms/scottlc/',
        'Sound and Moving Image Library' => '/rooms/smil/',
        'Steacie Science & Engineering Library' => '/rooms/steacie/',
        'Osgoode Hall Law School Library' => 'http://rooms.library.osgoode.yorku.ca/',
    ),
);

