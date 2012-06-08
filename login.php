<?php
    require_once('conn.php');
    require_once('configuration.php');
    require_once('global_includes.php');
    require_once('functions.php');
    
    if (isset($_REQUEST['logout']) && !(isset($_REQUEST['l']) || isset($_REQUEST['r']))) {
        setcookie($GLOBALS['cookieName'], '', time()-3600);
        setcookie($GLOBALS['cookieName_auth'], '', time()-3600);
        print '<script type="text/javascript">window.location = "login.php";</script>';
    } 
    if (isset($_COOKIE[$GLOBALS['cookieName']])) {
       print '<a href="index.php">access ' . strtolower($GLOBALS['brandname']) . '</a>&nbsp;|&nbsp;<a href="?logout">logout</a>';
    }
    
    if (isset($_REQUEST['l']) || isset($_REQUEST['r'])) {
        if ( ( isset($_REQUEST['username']) && isset($_REQUEST['password']) && isset($_REQUEST['l']) ) || 
             ( isset($_REQUEST['dusername']) && isset($_REQUEST['dpassword']) && isset($_REQUEST['r']) )
           )
        {
            $proof;
            if (isset($_REQUEST['l'])) {
               $u = $_REQUEST['username'];
               $p = $_REQUEST['password'];
             } else {
	       $u = $_REQUEST['dusername'];
               $p = $_REQUEST['dpassword'];
               $dp = $_REQUEST['dpassconf'];
	    }
echo 'perl ../ragnarok/Ragnarok.pm --generate=upass --username="' . $u . '" --password="' . $p . '" --returnusername';
            $uname = shell_exec(escapeshellcmd('perl ../ragnarok/Ragnarok.pm --generate=upass --username="' . $u . '" --password="' . $p . '" --returnusername'));
            $proof = shell_exec(escapeshellcmd('perl ../ragnarok/Ragnarok.pm --generate=upass --username="' . $u . '" --password="' . $p . '"'));
	    if (isset($_REQUEST['l'])) {
                # Check if the proof  matches a record
                $result = mysql_query("SELECT * FROM `" . $GLOBALS['userTable'] . "` WHERE proof='" . mysql_real_escape_string($proof) . "' LIMIT 1;");
                $row = mysql_fetch_assoc($result);
                if ($row) {
                    setcookie($GLOBALS['cookieName'], $u);
                    setcookie($GLOBALS['cookieName_auth'], $uname);
                    print '<script type="text/javascript">window.location = "login.php";</script>';
                } else {
                    $error = "Incorrect login information.";
                }
                
            } elseif (isset($_REQUEST['r'])) {
                if ($u && $p && $dp && $p == $dp) {
                    # Check if the username proof exists, if not, add the username proof and the proofcode to the database
                    $result = mysql_query("SELECT * FROM `" . $GLOBALS['userTable'] . "` WHERE username='" . mysql_real_escape_string($uname) . "' LIMIT 1;");
                    $row = mysql_fetch_assoc($result);
                    if ($row) {
                        print "Username Exists.";
                    } else {
                        ob_start();
                        $system_command = 'useradd -d ' . getcwd() . '/' . $drive . mysql_real_escape_string($u) . '.git/ -m -g ' . $GLOBALS['linuxGroup'] . " -p " . system("perl -e 'print crypt(" . mysql_real_escape_string($p) . ", " . mysql_real_escape_string($u) . ")'") . ' ' . mysql_real_escape_string($u);
                        runCommandAsRoot($system_command);
                        setupGit(mysql_real_escape_string($u));
                        setupSSH($u);
                        $message = "Registered.";
                        ob_end_clean();
                    }
                } else {
                    if ($p != $dp) {
                        $error = 'Password and Password Confirmation do not match.';
                    } else {
                        $error = 'Incomplete Registration Form.';
                    }
                }
            }
        } 
    }

echo '<div id="logo"><img src="' . $GLOBALS['logo'] . '" alt="' . $GLOBALS['brandname'] . '" /></div>';

if (isset($message)) {
?>
<div id="message"><?php echo $message; ?></div>
<?php
}
if (isset($error)) {
?>
<div id="error"><?php echo $error; ?></div>
<?php
}

?>

<table>
<tr>
<td>
<div id="login">
<h2>Login</h2>
<form method="post" action="">
<table>
<tr>
<td>Username:</td><td>&nbsp;</td><td><input type=text name="username" /></td>
</tr>
<tr>
<td>Password:</td><td>&nbsp;</td><td><input type=password name="password" /></td>
</table>
<input type=submit name=l value="Login" />
</form>
</div>
</td>
<td>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</td>
<td>
<div id="register">
<h2>Register</h2>
<form method="post" action="">
<table>
<tr>
<td>Desired Username:</td><td>&nbsp;</td><td><input type=text name="dusername" /></td>
</tr>
<tr>
<td>Desired Password:</td><td>&nbsp;</td><td><input type=password name="dpassword" /></td>
</tr>
<tr>
<td>Password Confirmation:</td><td>&nbsp;</td><td><input type=password name="dpassconf" /></td>
</tr></table>
<input type=submit name=r value="Register" />
</form>
</div>
</td>
</tr>
</table>
<div id="logo" style="position:fixed;bottom:5px; right: 5px;">
<img src="../ragnarok/logo/using.png" alt="Ragnarok Encryption Used Here" />
</div>
