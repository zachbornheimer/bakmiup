<?php
require_once('functions.php');
require_once('configuration.php');
if (file_exists('runme.sh')) {
    die("Please run <code>runme.sh</code> by running: <code> sh runme.sh</code> as root before continuing.");
}
mysql_connect($GLOBALS['mysql_server'], $GLOBALS['mysql_username'], $GLOBALS['mysql_password']);
mysql_select_db($GLOBALS['mysql_database']) or setup();
?>
