<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

########################################################################
### Edit your own details and then rename the file configuration.php ###
### Note: every variable value can be changed.  Anything put in are  ###
###       suggestions of some sort (value or type suggestions)       ###
########################################################################


# MYSQL Connections
global $mysql_server, $mysql_username, $mysql_password, $userTable;

$GLOBALS['mysql_server'] = 'probably localhost';
$GLOBALS['mysql_username'] = 'username';
$GLOBALS['mysql_password'] = 'password';
$GLOBALS['mysql_database'] = 'bakmiup';
$GLOBALS['userTable'] = 'bakmiup_users';

# Internals
global $cookieName, $cookieName_auth, $linuxGroup;
$GLOBALS['cookieName'] = 'bakmiup';
$GLOBALS['cookieName_auth'] = $GLOBALS['cookieName'] . '_auth';
$GLOBALS['linuxGroup'] = 'bakmiupers';
$GLOBALS['drive'] = 'drive/'; # trail with a / and it must be in the same dir

# UI Preferences
global $brandname, $logo, $registrationOpen; 
$GLOBALS['brandname'] = 'bakmiup';
$GLOBALS['logo'] = 'images/bakmiup.png';
$GLOBALS['registrationOpen'] = false; # Is Registration Open?

# SSH Server Settings
global $server, $port;
$GLOBALS['server'] = 'server';
$GLOBALS['port'] = '22';
?>
