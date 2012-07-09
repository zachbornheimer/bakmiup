<?php
/*
    restore.php - Allows the user to download the software to restore
                  the latest backup from the current users account.

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

    if (isset($_REQUEST['download'])) {
        if (isset($_REQUEST['win']))
            generateRestoreScript('win');
        if (isset($_REQUEST['mac']))
            generateRestoreScript('mac');
        if (isset($_REQUEST['linux']))
            generateRestoreScript('linux');
    }
?>
<!doctype html>
<head>
<title>Download the Latest Backup</title>
</head>
<div id="nav">
<a href="index.php">Go To the <?php echo $GLOBALS['brandname']; ?> home screen.</a>
</div>
<h1>Download the Latest Backup</h1>
<?php if (!isset($_REQUEST['download'])) { ?>
<div id="links">
<a href="restore.php?download&win">Download the Windows Software</a><br />
<a href="restore.php?download&mac">Download the Mac Software</a><br />
<a href="restore.php?download&linux">Download the Linux Software</a><br />
</div>
<?php } else { ?>
Make sure you put the files where they belong once you get them.  Also makes sure to run the program as an administrator!
<div id="ready">
<ul><li><a href="<?php echo 'download/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . 'r.zip'; ?>">The software is ready.  Get it now!</a></div></li></ul>

<?php } ?>
