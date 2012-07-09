<?php
    require('configuration.php');
    require('conn.php');
    if (!file_exists('php_root') || !file_exists('updater.pl')) {
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
