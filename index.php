<?php
require_once('configuration.php');
require_once('auth.php');
?>
<div id="logo"><img src="<?php echo $GLOBALS['logo']; ?>" alt="<?php echo $GLOBALS['brandname']; ?>" /></div>
<ul>
<li><a href="new.php">Backup this computer</a></li>
<li><a href="log.php">View the Backup Log</a></li>
<li><a href="restore.php">Download the Most Recent Backup</a></li>
<li><a href="login.php?logout">Logout</a></li>
</ul>

<br /><br />
<div id="license">
<a href="http://www.gnu.org/licenses/gpl-3.0-standalone.html"><?php echo $GLOBALS['brandname']; ?> is licensed via the GPL v3</a><br />
Copyright &copy; Z. Bornheimer and Zysys.
</div>
