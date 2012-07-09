<?php
/*
    viewlogfiles.php - The place to view the changed files from a particular commit.

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

    $commitId = $_REQUEST['c']; 

?>
<!doctype html>
<head>
<title>View Changed Files in the Backup</title>
</head>
<body>
<div id="nav"><a href="index.php">Go to the <?php echo $GLOBALS['brandname']; ?> home screen.</a></div>
<h1>View Changed Files in the Backup</h1>
<pre>
<?php echo displayChangedFilesInCommit($commitId); ?>
</pre>
</body>
