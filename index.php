<?php
require_once('auth.php');
?>
<div id="logo"><img src="<?php echo $GLOBALS['logo']; ?>" alt="<?php echo $GLOBALS['brandname']; ?>" /></div>
<ul>
<li><a href="new.php">Backup this computer</a></li>
<li><a href="log.php">View the Backup Log</a></li>
<li><a href="restore.php">Download the Most Recent Backup</a></li>
<li><a href="login.php?logout">Logout</a></li>
</ul>
