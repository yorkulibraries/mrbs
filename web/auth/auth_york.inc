<?php

/* $Id$
 *
 * Dummy authentication scheme, that accepts any user.
 * Useful for using mrbs without authentication,
 * or in combination with a session scheme that already enforces
 * its own authentication. For example that in IIS.
 *
 * To use this authentication scheme set the following
 * things in config.inc.php:
 *
 *      $auth["type"]    = "york";
 *      $auth["session"] = your choice
 *
 * Then, you may configure admin users:
 *
 * $auth["admin"][] = "nt_username1";
 * $auth["admin"][] = "nt_username2";
 *
 * See AUTHENTICATION  for more informations.
 */

/** 
 * Checks if the specified username/password pair are valid.
 *
 * For this authentication scheme always validates.
 * 
 * @param   string  $user   The user name
 * @param   string  $pass   The password
 * @return  int             non-zero - Always valid
 */
function authValidateUser($user, $pass)
{
  return 1;
}

/* authGetUserLevel($user)
 * 
 * Determines the users access level
 * 
 * $user - The user name
 *
 * Returns:
 *   The users access level
 */
function authGetUserLevel($user)
{
  global $auth, $area;
  $admins = $auth['admin'];
  // User not logged in, user level '0'
  if (!isset($user))
  {
    return 0;
  }

  // Check if the user is can modify
  for ($i = 0; isset($admins[$i]); $i++)
  {
    if (strcasecmp($user, $admins[$i]) == 0)
    {
      return 2;
    }
  }
  
  $area_name = get_area_name($area);
  $groups = get_user_groups();
  if (isset($auth['allowed_user_groups_for_area'][$area_name]) && !empty($auth['allowed_user_groups_for_area'][$area_name])) {
    if (!empty($groups)) {
      foreach ($groups as $group) {
        if (in_array($group, $auth['allowed_user_groups_for_area'][$area_name])) {
          return 1;
        }
      }
    }
    return 0;
  } else {
    if (isset($auth['allowed_user_groups']) && !empty($auth['allowed_user_groups'])) {
      if (!empty($groups)) {
        foreach ($groups as $group) {
          if (in_array($group, $auth['allowed_user_groups'])) {
            return 1;
          }
        }
      }
      return 0;
    }
  }
  // Everybody else is access level '1'
  return 1;
}

