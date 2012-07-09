<?php
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
