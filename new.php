<?php
/*
    new.php - Allows the user to download the software to start a new backup regimine.

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
require_once('functions.php');
require_once('auth.php');
ob_start();
if (isset($_REQUEST['download'])) {
    $exclusionArray = array('*.' . $GLOBALS['brandname'] . '_patch');
    if (!(@$_REQUEST['gitignoreExclusion']))
        $exclusionArray[] = '!.gitignore';

    if (@$_REQUEST['appVista'])
        if (isset($_REQUEST['win']))
            $exclusionArray[] = 'AppData/';

    if (@$_REQUEST['appXP'])
        if (isset($_REQUEST['win']))
            $exclusionArray[] = 'Application Data/';

    if (@$_REQUEST['localsettings'])
        if (isset($_REQUEST['win']))
            $exclusionArray[] = 'Local Settings/';

    if (@$_REQUEST['libraryExclusion'])
        if (isset($_REQUEST['mac']))
            $exclusionArray[] = 'Library/';

    if (@$_REQUEST['iTunesExclusion'])
        $exclusionArray[] = 'iTunes/';

    if (@$_REQUEST['trashExclusion'])
        if (isset($_REQUEST['mac']))
            $exclusionArray[] =  '.Trash/';

    if (@$_REQUEST['hiddenExclusion'])
        $exclusionArray[] = '.*';

    if (isset($_REQUEST['win'])) {
        $exclusionArray[] = "ntuser.dat*";
        $exclusionArray[] = "NTUSER.DAT*";
        $exclusionArray[] = "ntuser.ini*";
        $exclusionArray[] = "NTUSER.INI*";
    }
    $i = 1;

    while (@$_REQUEST['exclusion' . $i]) {
        if ($_REQUEST['exclusion' . $i] != "")
            $exclusionArray[] = $_REQUEST['exclusion' . $i];
        $i++;
    }

    if (isset($_REQUEST['mac']) || isset($_REQUEST['linux']))
        $available = generateOSCode($exclusionArray, false); 

    if (isset($_REQUEST['win']))
        $available = generateOSCode($exclusionArray, true);

}
ob_end_clean();
?>
<!doctype html>
<head>
<title>Registration for <?php echo $GLOBALS['brandname']; ?></title>
<style type="text/css">
li {
    list-style-type: none;
    padding: 5px;
}
code {
    font-style: normal;
}
</style>
</head>
<div id="nav">
<a href="index.php">Go to the <?php echo $GLOBALS['brandname']; ?> home screen.</a>
</div>
<br />
<div id="note">
Note: This assumes that git is already installed.  If it is not, navigate to <a href="http://git-scm.com">git-scm.com</a> and install it before continuing.  On Windows, you must install git with the option selected to overwrite windows tools (it will be the third option during installation), but use the defaults for everything else.<br />
Be aware, only one computer can be linked to each account. One computer is permanently assigned to one account.  This is to prevent accedental file transfer problems and prevent security breaches.
<br /><br />If possible, run the scripts as an Administrator or as a Super User as to prevent unexpected permission errors during the initial setup and during the scheduled execution phase.</div>
<?php
if (isset($available)) {
    ?>
<div id="ready">
<ul><li><a href="<?php echo 'download/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '.zip'; ?>">The software is ready.  Get it now!</a></div></li></ul>
<?php
} else {
?>
<form id="generateForm" name="generateForm" action="" method="post">
<div id="exclusions">
<h2>Exclusions:</h2>
<input type="checkbox" name="libraryExclusion" value="1" checked>&nbsp;Library (Mac Only)</input>
<input type="checkbox" name="iTunesExclusion" value="1" checked>&nbsp;iTunes</input>
<inpuut type="checkbox" name="trashExclusion" value="1" checked>&nbsp;Trash</input>
<input type="checkbox" name="hiddenExclusion" value="1" checked>&nbsp;Hidden Files and Directories (not including .gitignore)</input>
<input type="checkbox" name="gitignoreExclusion" value="1">&nbsp;.gitignore</input>
<input type="checkbox" name="appVista" value="1" checked>&nbsp;AppData</input>
<input type="checkbox" name="localsettings" value="1" checked>&nbsp;Local Settings</input>
<input type="checkbox" name="appXP" value="1" checked>&nbsp;Application Data</input>
<br /><br />
<h3>Additional Exclusions:</h3>
<span style="font-style: italic">Add each folder followed by a slash.  Files can be inputted as well.  Example: <code>iTunes/</code> or <code>Thumbs.db</code></span>
<br />
<div id="exclusionItemsDiv"><ul><p id="exclusionItems"></p></ul></div>
<p><input type="button" value="Add A New Exclusion Item" onclick="javascript:add_new_exclusion()" /></p>
<input type="hidden" name="download" />
<input type="hidden" name="available" />
<input style="visibility:hidden" type="submit" id="submit" />
<script type="text/javascript">
var exclusionNum = 0;
function add_new_exclusion() {
    exclusionNum++;
    // Derived From: http://j.mp/KV9Bma 
    var mydiv = document.getElementById("exclusionItemsDiv");
    var newcontent = document.createElement('div');
    newcontent.innerHTML = "<li><input type='text' id='exclusion" + exclusionNum + "' name='exclusion" + exclusionNum + "' /></li>";

    while (newcontent.firstChild) {
        mydiv.appendChild(newcontent.firstChild);
    }
    // End Derivation
    document.getElementById("exclusion" + exclusionNum).focus();
}
function generate_link() {
    document.getElementById("submit").click();
}
</script>
</div>
Note: Windows XP is not yet supported.
<div id="downloadLinks">
<ul>
<li><input type="submit" name="mac" value="Download the Mac Software" /></li>
<li><input type="submit" name="linux" value="Download the Linux Software" /></li>
<li><input type="submit" name="win" value="Download the Windows Software" /></li>
</ul>
</div>
</form>
<?php
}
?>
</body>
</html>
