<?php

/*
    configuration-template.php - All the changes that need to be made to operate bakmiup.
                                 Don't forget to rename this file configure.php

    Note, GPL v3 states that, if you reject the license, you may not modify or
    distribute this software.  This and only this page is exempted from this
    clause because you would  not be able to run the code without modifying this file.

    bakmiup - the git-based backup client
    Copyright (C) 2012  Z. Bornheimer and Zysys

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

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

// The following line must only stay in exactly the same syntax with a 0 or 1 as the value assigned.
// Overwrite files during update (default is yes/1)
$overwriteUpdatedFiles = 1;


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
