<?php
/*
    auth.php - Authenticates the user and checks for integretity amungst the system.

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
    require_once('configuration.php');
    require_once('conn.php');

    if (!file_exists('php_root') || !file_exists('updater.pl') || !phprootPermissionsAreGood() || !phprootOwnershipCorrect()) {
        setup();
        exit;
    }
    if (isset($_COOKIE[$GLOBALS['cookieName_auth']])) {
        $result = mysqli_query($link, 'SELECT * FROM `' . $GLOBALS['userTable']  . '` WHERE username = ' . "'" . mysqli_real_escape_string($link, $_COOKIE[$GLOBALS['cookieName_auth']]) . "'" . ' LIMIT 1;');
        $row = mysqli_fetch_assoc($result);
        if (!$row) {
            print '<script type="text/javascript">window.location="login.php"</script>';
        }
     } else {
         print '<script type="text/javascript">window.location="login.php"</script>';
     }






?>
