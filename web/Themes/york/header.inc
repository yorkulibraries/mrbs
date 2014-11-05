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
  
  $booking_rules = get_booking_rules();
  global $global_nav_links;  
?>

<html>
  <head>
  
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <title><?php echo get_vocab('global_title') ?></title>
  
  <?php
  require_once "style.inc";
  require_once "js.inc";
  ?>
  
  <!-- Latest compiled and minified CSS -->
  <link media="screen,projection,tv" rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

  <!-- Optional theme -->
  <link media="screen,projection,tv" rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">

  <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="css/bootstrap3-wysihtml5.min.css">
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
          <a class="navbar-brand" href="index.php"><?php echo get_vocab("app_home") ?></a>
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
	                <a role="menuitem" tabindex="-1" href="edit_holidays.php?area=<?php echo $area; ?>"><?php echo get_vocab("holidays"); ?></a>
	              </li>
	              <li role="presentation">
	                <a role="menuitem" tabindex="-1" href="edit_rules.php?area=<?php echo $area; ?>"><?php echo get_vocab("booking_rules"); ?></a>
	              </li>
	              <li role="presentation">
	                <a role="menuitem" tabindex="-1" href="edit_admins.php?area=<?php echo $area; ?>"><?php echo get_vocab("administrators"); ?></a>
	              </li>
								<li role="presentation">
	                <a role="menuitem" tabindex="-1" href="report.php"><?php echo get_vocab("report"); ?></a>
	              </li>
	            </ul>
	          </li>
	          <?php } ?>
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
	          <?php foreach($global_nav_links as $menu_label=>$menu_link) { ?>
	            <?php if (is_array($menu_link)) { ?>
	              <li class="dropdown">
    	            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo get_vocab($menu_label); ?> <span class="caret"></span></a>
    	            <ul class="dropdown-menu" role="menu">
    	              <?php foreach($menu_link as $sub_menu_label=>$sub_menu_link) { ?>
    	                <li role="presentation">
                        <a role="menuitem" tabindex="-1" href="<?php echo $sub_menu_link; ?>"><?php echo get_vocab($sub_menu_label); ?></a>
                      </li>
    	              <?php } ?>
    	            </ul>
    	          </li>
	            <?php } else { ?>
	              <li role="presentation">
                  <a role="menuitem" tabindex="-1" href="<?php echo $menu_link; ?>"><?php echo get_vocab($menu_label); ?></a>
                </li>
	            <?php } ?>
	          <?php } ?>
	        </ul>
	      </div>
      </div>
    </nav>

<div id="contents" class="container">
	<h1><?php echo get_vocab('pagetitle'); ?></h1>
	<?php if ($booking_rules) { ?>
	<p><a class="booking-rules" data-toggle="modal" data-target="#booking_rules"><?php echo get_vocab('booking_rules'); ?></a></p>
  <!-- Modal -->
  <div class="modal fade" id="booking_rules" tabindex="-1" role="dialog" aria-labelledby="booking_rules_label" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="booking_rules_label"><?php echo get_vocab('booking_rules'); ?></h4>
        </div>
        <div class="modal-body">
          <?php echo $booking_rules; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo get_vocab('Close'); ?></button>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>	
<?php

} // end of print_theme_header()

