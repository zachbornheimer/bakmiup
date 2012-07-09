<?php
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
<a href="index.php">Go To the bakmiup home screen.</a>
</div>
<h1>Bakup Records</h1>
<pre>
<?php echo displayGitLog('makeLinks'); ?>
</pre>
</body>
</html>
