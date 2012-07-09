<?php
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
Make sure you put the files where they belong once you get them.
<div id="ready">
<ul><li><a href="<?php echo 'download/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . 'r.zip'; ?>">The software is ready.  Get it now!</a></div></li></ul>

<?php } ?>
