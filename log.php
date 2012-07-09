<?php
/*
    log.php - Allows the user to see the log file of the backups.  Allows the user
              to click on the commit id to view the changed files in the commit.

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
    require_once('functions.php');
?>
<!doctype html>
<head>
<title>Backup Records</title>
</head>
<body>
<div id="nav">
<a href="index.php">Go To the <?php echo $GLOBALS['brandname']; ?> home screen.</a>
</div>
<h1>Bakup Records</h1>
<p>Click on the link to display the files that were changed in that particular backup.</p>
<pre>
<?php echo displayGitLog('makeLinks'); ?>
</pre>
</body>
</html>
