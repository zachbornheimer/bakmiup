<?php
/*
    index.php - the main hub of bakmiup

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
<br />
<div id="version">
version <?php echo file_get_contents("VERSION"); ?>
</div>
