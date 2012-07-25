<?php
/*
    conn.php - An included file that connects the application and the database.

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
require_once('functions.php');
require_once('configuration.php');
if (file_exists('runme.sh')) {
    die("Please run <code>runme.sh</code> by running: <code> sh runme.sh</code> as root before continuing.");
}
mysql_connect($GLOBALS['mysql_server'], $GLOBALS['mysql_username'], $GLOBALS['mysql_password']) or checkSpace();
mysql_select_db($GLOBALS['mysql_database']) or setup();
?>
