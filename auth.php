<?php
    require_once('configuration.php');
    require_once('conn.php');

    if (!file_exists('php_root') || !file_exists('updater.pl') || !phprootPermissionsAreGood() || !phprootOwnershipCorrect()) {
        setup();
        exit;
    }
    if (isset($_COOKIE[$GLOBALS['cookieName_auth']])) {
        $result = mysql_query('SELECT * FROM `' . $GLOBALS['userTable']  . '` WHERE username = ' . "'" . mysql_real_escape_string($_COOKIE[$GLOBALS['cookieName_auth']]) . "'" . ' LIMIT 1;');
        $row = mysql_fetch_assoc($result);
        if (!$row) {
            print '<script type="text/javascript">window.location="login.php"</script>';
        }
     } else {
         print '<script type="text/javascript">window.location="login.php"</script>';
     }






?>
