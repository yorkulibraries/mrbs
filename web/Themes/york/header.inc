<?php

// $Id: header.inc 2784 2013-11-21 10:48:22Z cimorrison $


// Print the page header
function print_theme_header($day, $month, $year, $area, $room)
{
  global $mrbs_company, $mrbs_company_logo, $mrbs_company_url, $mrbs_company_more_info,
         $search_str, $locale_warning;
  global $tbl_entry, $tbl_room, $tbl_area;
  global $PHP_SELF, $HTTP_HOST, $QUERY_STRING;
  global $view_week_number, $weekstarts, $times_along_top, $periods, $enable_periods;
  global $auth, $max_level;
  global $default_language_tokens, $disable_automatic_language_changing, $override_locale;
  global $select_options;
  global $ajax_refresh_rate;
  global $main_table_cell_border_width, $main_cell_height;
  global $timetohighlight;
  
  $page = basename($PHP_SELF, ".php");
  $user = getUserName();
  $is_admin = (authGetUserLevel($user) >= $max_level);
  
  // Need to set the timezone before we can use date()
  get_area_settings($area);

  // If we dont know the right date then make it up 
  if (!$day)
  {
    $day   = date("d");
  }
  if (!$month)
  {
    $month = date("m");
  }
  if (!$year)
  {
    $year  = date("Y");
  }
  if (!isset($search_str))
  {
    $search_str = "";
  }
  
  http_headers();
  echo DOCTYPE;
?>

<html>
  <head>
  
  <meta charset="<?php echo get_charset(); ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <title><?php echo get_vocab("mrbs") ?> | York University Libraries</title>
  
  <?php
  require_once "style.inc";
  require_once "js.inc";
  ?>
  
  <!-- Latest compiled and minified CSS -->
  <link media="screen,projector" rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

  <!-- Optional theme -->
  <link media="screen,projector" rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">

  <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

  <link href="css/york.css" rel="stylesheet" media="all">

  </head>
  
  <?php
  // Put the filename in as a class to aid styling.   
  // (Use a class rather than id to avoid specificity problems)
  echo "<body class=\"non_js ".htmlspecialchars($page)."\">\n";
   
    // Add a class of "js" so that we know if we're using JavaScript or not
    // and remove the non_js class (it's sometimes useful to know that we're
    // not running JavaScript)
  ?>
    <script type="text/javascript">
      //<![CDATA[
      $('body').addClass('js').removeClass('non_js');
      //]]>
    </script> 
    
    <a class="sr-only" href="#contents">Skip to main content</a>

    <nav class="navbar navbar-default yul-navbar" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Navigation menu</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a title="York University" href="http://www.yorku.ca/" class="navbar-brand"><img src="yib11yorklogo.gif" alt="York University"/></a>
          <a class="navbar-brand" href="index.php"><?php echo get_vocab("mrbs") ?></a>
        </div>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav navbar-right">
						<?php if ($is_admin) { ?>
						<li class="dropdown">
	            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin <span class="caret"></span></a>
	            <ul class="dropdown-menu" role="menu">
	              <li role="presentation">
	                <a role="menuitem" tabindex="-1" href="admin.php"><?php echo get_vocab("rooms")?></a>
	              </li>
								<li role="presentation">
	                <a role="menuitem" tabindex="-1" href="edit_area_hours.php?area=<?php echo $area; ?>"><?php echo get_vocab("areahours"); ?></a>
	              </li>
								<li role="presentation">
	                <a role="menuitem" tabindex="-1" href="report.php"><?php echo get_vocab("report"); ?></a>
	              </li>
	            </ul>
	          </li>
	          <?php } ?>
	          <?php if (empty($user)) { ?>
	          <li role="presentation">
              <a role="menuitem" tabindex="-1" href="login.php"><span class="fa fa-sign-in"></span> <?php echo get_vocab('login'); ?></a>
            </li>
            <?php } ?>
						<?php if (!empty($user)) { ?>
						<li class="dropdown">
	            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-user"></span> <?php echo $user; ?> <span class="caret"></span></a>
	            <ul class="dropdown-menu" role="menu">
					  		<li role="presentation">
							    <a role="menuitem" tabindex="-1" href="logout.php"><span class="fa fa-sign-out"></span> <?php echo get_vocab("logoff"); ?></a>
							  </li>
							</ul>
						</li>
						<?php } ?>
					</ul>
					
	        <ul class="nav navbar-nav">
						<li class="dropdown">
	            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo get_vocab($default_language_tokens); ?> <span class="caret"></span></a>
	            <ul class="dropdown-menu" role="menu">
								<li role="presentation">
	                <a role="menuitem" tabindex="-1" href="index.php?mylang=en"><?php echo get_vocab('en'); ?></a>
	              </li>
								<li role="presentation">
	                <a role="menuitem" tabindex="-1" href="index.php?mylang=fr"><?php echo get_vocab('fr'); ?></a>
	              </li>
							</ul>
						</li>
						<li class="dropdown">
	            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Libraries with Group Study Rooms <span class="caret"></span></a>
	            <ul class="dropdown-menu" role="menu">
								<li role="presentation">
	                <a role="menuitem" tabindex="-1" href="/rooms/frost/">Leslie Frost Library (Glendon Campus)</a>
	              </li>
								<li role="presentation">
	                <a role="menuitem" tabindex="-1" href="/mrbs/day.php?group=1&area=2">Peter F. Bronfman Business Library</a>
	              </li>
								<li role="presentation">
	                <a role="menuitem" tabindex="-1" href="/mrbs/day.php?group=1&area=1">Scott Library</a>
	              </li>
								<li role="presentation">
	                <a role="menuitem" tabindex="-1" href="/mrbs/day.php?group=1&area=43">Scott Library Learning Commons</a>
	              </li>
								<li role="presentation">
	                <a role="menuitem" tabindex="-1" href="/mrbs/day.php?group=1&area=34">Sound and Moving Image Library</a>
	              </li>
								<li role="presentation">
	                <a role="menuitem" tabindex="-1" href="/mrbs/day.php?group=1&area=5">Steacie Science &amp; Engineering Library</a>
	              </li>
	            </ul>
	          </li>
	        </ul>
	      </div>
      </div>
    </nav>

<div id="contents" class="container">
	<h1><?php echo get_vocab('pagetitle'); ?></h1>
<?php

} // end of print_theme_header()

