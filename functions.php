<?php
/*
    functions.php - All the functions for the operation of bakmiup.

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

// Some irrelevant constants. =====================================================

define('PHP_ROOT', 'php_root'); // How bakmiup will interact with the system as root.

// ================================================================================


require_once('configuration.php');
require_once('conn.php');
function setup() {
    ob_start();
    $message = '<div id="nav"><a href="index.php">Go to the '.$GLOBALS['brandname'].' home screen.</a></div><br />';
    $message .= serverSetup();
    $message .= "Running setup becuase I cannot find something or because something is broken...<br /><br />";
    $message .= "If any errors occur, cd to " . getcwd() . " and, as root, type the following verbatim: <code>cd ../; chmod 777 -R " . getcwd() ."; chown -R " . system("whoami") . ":" . system("whoami") . " " . getcwd() . "; cd " . getcwd() . ";</code><br />";
    ob_end_clean();    
    echo $message;
    if (!file_exists('updater.pl')) {
        echo "Setting up the alien updater...<br />";
        updaterSetup();
    }
    echo 'Setting up the database, if necessary...<br />';
    mysqli_query($link, 'CREATE DATABASE IF NOT EXISTS ' . $GLOBALS['mysql_database']);
    echo 'Setting up users table, if necessary...<br />';
    mysqli_select_db($link, $GLOBALS['mysql_database']); 
    setupTable("userTable");
    echo "&nbsp;&nbsp;&nbsp;&nbsp;...done.";
    die;
}

function checkSpace($die = true) {
    ob_start();
    $cmd = <<<'ENDCOMMAND'
perl -e '$var=`df \`pwd\``;$var=~s/.*Use%.*\n//;$var=~s/.*\s+(\d+)%.*/$1/gm;print $var;'
ENDCOMMAND;
    $df = system($cmd);
    ob_end_clean();
    if ($df == 100) {
        print "Fatal Error: you're all out of space. <a href='http://zysys.org/wiki/index.php/Bakmiup:_Error_Codes#Error_1050'>Error Code: 1050</a>";
        if ($die)
            exit;
    }
    $cmd = <<<'ENDCOMMAND'
perl -e '$var=`df /var`;$var=~s/.*Use%.*\n//;$var=~s/.*\s+(\d+)%.*/$1/gm;print $var;'
ENDCOMMAND;
    ob_start();
    $df = system($cmd);
    ob_end_clean();
    if ($df == 100) {
        print "Fatal Error: you're all out of space. <a href='http://zysys.org/wiki/index.php/Bakmiup:_Error_Codes#Error_1095'>Error Code: 1095</a>";
        if ($die)
            exit;
    }
    $cmd = 'perl -e ' . "'\$var=`df " . '"' . getcwd() . '/' . $GLOBALS['drive'];
    $cmd .= <<<'ENDCOMMAND'
"`;$var=~s/.*Use%.*\n//;$var=~s/.*\s+(\d+)%.*/$1/gm;print $var;'
ENDCOMMAND;
    ob_start();
    $df = system($cmd);
    ob_end_clean();
    if ($df == 100) {
        print "Fatal Error: you're all out of space. <a href='http://zysys.org/wiki/index.php/Bakmiup:_Error_Codes#Error_1257'>Error Code: 1257</a>";
        if ($die)
            exit;
    }
}

function updaterSetup() {
    if (!file_exists('updater.pl')) {
        $f = 'updater.pl';
        $fh = fopen($f, 'w') or die ("can't open $f");
        $file = <<<'CONTENT'
#!/usr/bin/perl
use strict;
use warnings;
use LWP::Simple;
use LWP::UserAgent;

########################################################################################
#    updater.pl - the alien updater for bakmiup
#
#    alien - Autonomous Linked Instruction Execution Network
#    Copyright (C) 2012  Z. Bornheimer and Zysys
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
########################################################################################

my $ua = LWP::UserAgent->new;
my $productName = 'bakmiup';
if (! -e "VERSION") {
    die('No VERSION file here...');
}
open(F, "VERSION");
my $version = <F>;
close(F);
my $url = "http://alien.zysys.org/updater.php?productName=" . $productName . "&version=" . $version;
if (get($url) =~ /^http/) {
    my $req = HTTP::Request->new(GET => get($url));
    my $res = $ua->request($req);
    if ($res->is_success) {
        open (F, '>update.zip');
        print F $res->content;
        close(F);
        my $overwriteUpdatedFiles = 1;
        open (F, 'configuration.php');
        while(<F>) {
            if ($_ =~ /overwriteUpdatedFiles/) {
                $_ =~ s/.*=\s*(.);/$1/;
                $overwriteUpdatedFiles = $_;
            }
        }
        close(F);
        my $dir;
        if ($overwriteUpdatedFiles) {
            $dir = `unzip -o update.zip`;
        } else {
            $dir = `unzip update.zip`;
        }
        my @info = split("\n", $dir);
        my $expectedParent;
        foreach (@info) {
            if (/creating/) {
                $_ =~ s/^\s*creat.*: //;
                if (!$expectedParent) {
                    $expectedParent = $_;
                }
            }
        }
        if ($expectedParent) {
            `cd $expectedParent; mv * ..; mv .* ..; cd ..; rmdir $expectedParent;`;
        }
        unlink('update.zip');
    }
}
CONTENT;
        fwrite($fh, $file);
        fclose($fh);
        $phprootDNE = !file_exists(PHP_ROOT);
        if (!$phprootDNE)
            $f = "runthis.sh";
        else
            $f = "runme.sh";
        $fh = fopen($f, 'a') or die ("can't open $f");
        fwrite($fh, 'crontab -l >updatercron; echo "0 0 * * * cd ' . getcwd() . ';sudo perl updater.pl">>updatercron; crontab updatercron; rm updatercron $0;');
        fclose($fh);
        if (!$phprootDNE)
            system('./'.PHP_ROOT.';rm runthis.sh');
    }
}

function serverSetup() {
/*
This function should run if any of the following are true:

1) PHP_ROOT is missing
2) ownership on PHP_ROOT is incorrect
3) permissions on PHP_ROOT are incorrect
4) the group (default: bakmiupers) is not installed.

==================================================================
Version 0.0.0.2 fixes the bug where an individual can
prevent access to bakmiup by repeatedly calling the setup script.

Note: The setup script will run, but only to do the bare minimum.
      It will check php_root primarily and adjust what it needs
      to adjust (ownership, permissions, and existance)...although
      it will also install the linux group for the bakmiup users.
==================================================================

*/
    $phprootDNE = !file_exists(PHP_ROOT);
    if ($phprootDNE) {
        $f = 'wrapper.c';
        $fh = fopen($f, 'w') or die ("can't open $f");
        $file = <<<'CONTENT'
  #include <stdlib.h>
  #include <sys/types.h>
  #include <unistd.h>

  int
  main (int argc, char *argv[])
  {
     setuid (0);

     /* WARNING: Only use an absolute path to the script to execute,
      *          a malicious user might fool the binary and execute
      *          arbitary commands if not.
      *
      * From: http://stackoverflow.com/questions/8532304/execute-root-commands-via-php
      * */

     system ("/bin/sh runthis.sh");

     return 0;
   }
CONTENT;
        fwrite($fh, $file);
        fclose($fh);
    }
    // Determine what needs to be done
    system('egrep -i "^'. $GLOBALS['linuxGroup'] . '" /etc/group', $groupNotInstalled);
    if (!$phprootDNE) {
        ob_start();
        $phprootPermissionsIncorrect = !phprootPermissionsAreGood();
        $phprootOwnershipIncorrect = !phprootOwnershipCorrect();
        ob_end_flush();
    }
    if ($phprootOwnershipIncorrect || $phprootPermissionsIncorrect || $groupNotInstalled) {
        $f = 'runme.sh';
        $fh = fopen($f, 'w') or die ("can't open $f");
        if ($groupNotInstalled)
            fwrite($fh, 'groupadd ' . $GLOBALS['linuxGroup'] . ';');
        if ($phprootDNE)
            fwrite($fh, 'gcc wrapper.c -o '.PHP_ROOT.';');
        if ($phprootOwnershipIncorrect)
            fwrite($fh, 'chown root '.PHP_ROOT.';');
        if ($phprootPermissionsIncorrect)
            fwrite($fh, 'chmod u=rwx,go=xr,+s '.PHP_ROOT.';');
        if ($phprootDNE)
            fwrite($fh, 'rm wrapper.c;');
        fwrite($fh, 'rm $0');
        ob_start();
        return "<br />Don't forget to run <code>runme.sh</code> script as root by running <code>sh runme.sh</code>!<br /><br />";
    }
    return;
}

function phprootPermissionsAreGood() {
    ob_start();
    // Following line derived from: http://www.itworld.com/nls_unix_fileattributes_060309
    $phprootPermissions = system("perl -e '" . '$mode = (stat("'.PHP_ROOT.'"))[2];printf "%04o", $mode & 07777'."'");
    ob_end_clean();
    return ($phprootPermissions == 6755);
}

function phprootOwnershipCorrect() {
    ob_start();
    // Following line derived from: http://www.perlmonks.org/index.pl?node_id=638015
    $phprootOwner = system("perl -e '" . '$uid = (stat "test")[4];$_ = getpwuid($uid);$_=~s/(.*)x($uid).*/$1/;print;' . "'");
    ob_end_clean();
    return ($phprootOwner == 'root');
}
function setupTable($table) {
    switch ($table) {
        case "userTable":  
            setupUsersTable();   
            break;
        case "database":
             
    }
}
function setupUsersTable() {
    # http: //www.daniweb.com/web-development/php/threads/99756/check-if-mysql-table-exists
    $sql = 'SELECT * FROM ' . $GLOBALS["userTable"] . ';';
    $result = @mysqli_query($link, $sql);
    if (!$result) {
        $sql = 'CREATE TABLE `' . $GLOBALS['userTable'] . '` (
               `userid` bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
               `username` longtext NOT NULL,
               `proof` longtext NOT NULL
               ) COMMENT="";';

        mysqli_query($sql) or die(mysqli_error());
    }
}

function setupGit($repoName) {
    if (!(file_exists(getcwd() . '/' . $GLOBALS['drive'] . $repoName . '.git'))) {
        system("mkdir " . getcwd() . '/' . $GLOBALS['drive'] . $repoName . '.git');
    }
    system("mkdir " . getcwd() . '/' . $GLOBALS['drive'] . $repoName . '.git');
    runCommandAsRoot('cd ' . getcwd() . '/' . $GLOBALS['drive'] . $repoName . '.git; git init --bare;');
    runCommandAsRoot('chown -R ' . $repoName . ':' . $GLOBALS['linuxGroup'] . ' ' . getcwd() . '/' . $GLOBALS['drive'] . $repoName . '.git; chmod -R u+rw ' . getcwd() . '/' . $GLOBALS['drive'] . $repName . '.git');
    return true;
}

function setupSSH($u) {
    runCommandAsRoot(' su - ' . $u . ' -c " mkdir -p ~/.ssh;  ssh-keygen -q -t rsa -N ' . "''" . '  -f ~/.ssh/id_rsa;"');
}

function generateOSCode($exclusionArray, $win) {
    ob_start();
    runCommandAsRoot('rm -R /tmp/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']].'/*;mkdir /tmp/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '; chmod 777 /tmp' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '; chown '. system('whoami').':'.system('whoami'). ' ' . $GLOBALS['linuxGroup'] . ' /tmp/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']]);
    ob_end_clean();
    $original_location = getcwd(); 
    runCommandAsRoot('chmod 0755 /tmp/'.$GLOBALS['brandname'].$_COOKIE[$GLOBALS['cookieName']]); 
    chdir('/tmp/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']]);  
    if (!$win) {
        $f = 'writeit.sh';
    } else {
        $f = 'writeit.bat';
    }
    $fh = fopen($f, 'w') or die ("can't open $f"); 
    if (!$win) {
    fwrite($fh, '#!/bin/bash' . "\n");
    fwrite($fh, 'servername='.$GLOBALS['brandname'].";\n");
    fwrite($fh, 'shfile=${servername}_setup.sh;'."\n");   
    fwrite($fh, 'username='.$_COOKIE[$GLOBALS['cookieName']].";\n");  
    fwrite($fh, 'server='.$GLOBALS['server'].";\n");  
    fwrite($fh, 'port='.$GLOBALS['port'].";\n"); 
    } else {
    fwrite($fh, '@echo off' . "\n");
    fwrite($fh, 'set servername='.$GLOBALS['brandname']."\n");
    fwrite($fh, 'set shfile=%servername%_setup.bat'."\n");
    fwrite($fh, 'set username='.$_COOKIE[$GLOBALS['cookieName']]."\n");
    fwrite($fh, 'set server='.$GLOBALS['server']."\n");
    fwrite($fh, 'set port='.$GLOBALS['port']."\n");
    } 
    $rest = <<<'END_REST'
original_path=$(pwd);
cd ~;
home_path=$(pwd);
mkdir -p $HOME/.ssh;

END_REST;
if (!empty($exclusionArray)) {
    foreach ($exclusionArray as $val) {
        $gitignore .= "echo $val>>.gitignore\n";
    }
}
$rest .= $gitignore;
$rest .= <<<'END_REST'
mkdir ${servername}_$username;
cd ${servername}_$username;
echo cd $home_path >$shfile
echo 'if [ ! -f $HOME/.ssh/id_rsa ]; then
         ssh-keygen -t rsa -N "" -q -f ~/.ssh/id_rsa
      fi' >>$shfile
echo "cat '$HOME/.ssh/id_rsa.pub' | ssh ${username}@${server} -p $port 'cat >> .ssh/authorized_keys'" >>$shfile
echo "ssh-add $HOME/.ssh/id_rsa" >>$shfile
echo "if [ -f $HOME/.gitignore ]; then
      cat $HOME/.gitignore | ssh ${username}@${server} -p $port 'cat >> .gitignore'
      fi" >>$shfile
echo "cat ~/.ssh/id_rsa.pub | ssh ${username}@${server} -p $port 'cat >> .ssh/authorized_keys'" >>$shfile
echo "ssh-agent ssh-add $HOME/.ssh/id_rsa" >>$shfile 
echo git init >>$shfile
echo git config --global pack.windowMemory 10m >>$shfile
echo git config --global pack.threads 1 >>$shfile
echo 'git add . -v' >>$shfile;
echo 'git commit -am "Initial Commit."' >>$shfile;
echo git remote rm $servername >>$shfile
echo git remote add $servername ssh://${username}@${server}:${port}/~${username} >>$shfile
echo git push $servername master >>$shfile
echo 'crontab -l > bakup;' >>$shfile;
echo "echo $[ ( $RANDOM % 59 )  + 1 ] '* * * * /bin/bash ${home_path}/${servername}_${username}/run.sh '>>bakup;" >>$shfile;
echo 'crontab bakup' >>$shfile;
echo 'rm bakup' >>$shfile;
echo 'path=$(pwd)' >run.sh
echo cd $home_path >>run.sh
echo git add . -v >>run.sh
echo 'git commit -am "Backup for: `date`"' >>run.sh
echo git push $servername master >>run.sh
echo 'cd $path' >>run.sh 
sh $shfile;
rm $0;
END_REST;

if ($win) {
$rest = <<<'END_REST'
set original_path=%CD%
set home_path=%HOMEDRIVE%%HOMEPATH%
cd %home_path%
IF NOT EXIST .ssh md .ssh

END_REST;
$rest .= $gitignore;
$rest .= <<<'END_REST'
IF NOT EXIST %servername%_%username% md %servername%_%username%
cd "%servername%_%username%"
echo @echo off>%shfile%
echo cd "%home_path%" >>%shfile%
echo cd "%home_path%\.ssh\" >>%shfile%
echo IF NOT EXIST id_rsa ssh-keygen -t rsa -N "" -q -f "%home_path%\.ssh\id_rsa">>%shfile%
echo type id_rsa.pub ^| ssh %username%@%server% -p %port% "cat >> .ssh/authorized_keys" >>%shfile%
echo cd .. >>%shfile%
echo ssh-agent ssh-add "%home_path%/.ssh/id_rsa" >>%shfile%
echo schtasks /Create /SC HOURLY /tr '"%home_path%\%servername%_%username%\runner.vbs"' /TN %servername%_%username% >>%shfile%
echo cd "%servername%_%username%" >>%shfile%
echo part1.bat >>%shfile%
echo @echo off >part1.bat
echo set original_path=%%CD%% >>part1.bat
echo cd "%home_path%\%servername%_%username%" >>part1.bat
echo set main_path=%%CD%% >>part1.bat
echo cd "%home_path%" >>part1.bat
echo git.exe init . >>part1.bat
echo git.exe config --global pack.windowMemory 10m >>part1.bat
echo git.exe config --global user.email "%username%@%username%" >>part1.bat
echo git.exe remote rm %servername% >>part1.bat
echo git.exe remote add %servername% ssh://%username%@%server%:%port%/~%username% >>part1.bat
echo cd "%%main_path%%" >>part1.bat
echo start /low /b run.bat >>part1.bat
echo @echo off >run.bat
echo set origpath=%%CD%% >>run.bat
echo cd "%home_path%" >>run.bat
echo start /low /b /wait "" "%PROGRAMFILES(X86)%\Git\bin\git.exe" "add" "." "-v" >>run.bat
echo start /low /b /wait "" "%PROGRAMFILES(X86)%\Git\bin\git.exe" "commit" "-am" "Backup for: %%date%% %%time%%"" >>run.bat
echo start /low /b /wait "" "git.exe" "push" "%servername%" "master" >>run.bat
echo cd %%origpath%% >>run.bat 
echo Set WshShell = CreateObject("WScript.Shell") >runner.vbs
echo WshShell.Run chr(34) ^& "%home_path%\%servername%_%username%\run.bat" ^& Chr(34),0 >>runner.vbs
echo Set WshShell = Nothing >>runner.vbs
%shfile%
del %0
END_REST;
}


    fwrite($fh, $rest);
    fclose($fh);
    chdir($original_location);
    runCommandAsRoot('chmod -R 755 /tmp/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '; chown -R ' . $_COOKIE[$GLOBALS['cookieName']] . ':' . $GLOBALS['linuxGroup'] . $GLOBALS['linuxGroup'] . ' /tmp/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']]); 
    runCommandAsRoot('rm '.getcwd().'/download/'.$GLOBALS['brandname'].$_COOKIE[$GLOBALS['cookieName']].'.zip;cd /tmp/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '; mkdir -p ' . getcwd() . '/download/;  zip ' . getcwd() . '/download/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '.zip ./* ');
    return true;
}

function generateRestoreScript($os) {
    ob_start();
    runCommandAsRoot('rm -R /tmp/r/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']].'/*;mkdir /tmp/r/; mkdir /tmp/r/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '; chmod 777 /tmp' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '; chown '.system('whoami').':'.system('whoami') . ' /tmp/r/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']]);
    $original_location = getcwd();
    runCommandAsRoot('chmod 0755 /tmp/r/'.$GLOBALS['brandname'].$_COOKIE[$GLOBALS['cookieName']]);
    chdir('/tmp/r/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']]);
    if ($os != 'win')
        $f = 'restore.sh';
    else
        $f = 'restore.bat';
    $fh = fopen($f, 'w') or die ("can't open $f");
    if ($os != 'win') {
    fwrite($fh, '#!/bin/bash' . "\n");
    fwrite($fh, 'servername='.$GLOBALS['brandname'].";\n");
    fwrite($fh, 'username='.$_COOKIE[$GLOBALS['cookieName']].";\n");
    fwrite($fh, 'server='.$GLOBALS['server'].";\n");
    fwrite($fh, 'port='.$GLOBALS['port'].";\n");
    } else {
    fwrite($fh, '@echo off' . "\n");
    fwrite($fh, 'set servername='.$GLOBALS['brandname']."\n");
    fwrite($fh, 'set username='.$_COOKIE[$GLOBALS['cookieName']]."\n");
    fwrite($fh, 'set server='.$GLOBALS['server']."\n");
    fwrite($fh, 'set port='.$GLOBALS['port']."\n");
    }
    if ($os != 'win') {
        $rest = <<<'END_REST'
cd %HOME%
git clone ssh://${username}@${server}:${port}/~${username}
rm $0
END_REST;
    } else {
        $rest = <<<'END_REST'
cd %HOME%
git clone ssh://%username%@%server:%port%/~%username%
del %0
END_REST;
    }

    fwrite($fh, $rest);
    fclose($fh);
    chdir($original_location);
    runCommandAsRoot('rm '.getcwd().'/download/'.$GLOBALS['brandname'].$_COOKIE[$GLOBALS['cookieName']].'r.zip;chmod -R 755 /tmp/r/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '; chown -R ' . $_COOKIE[$GLOBALS['cookieName']] . ':' . $GLOBALS['linuxGroup'] . $GLOBALS['linuxGroup'] . ' /tmp/r/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']]);
    runCommandAsRoot('cd /tmp/r/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '; mkdir -p ' . getcwd() . '/download/;  zip ' . getcwd() . '/download/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . 'r.zip ./* ');
    ob_end_clean();
    return true;

}

function displayGitLog($arg = false) {
    $makeLinks = ($arg == 'makeLinks');
    $homedir = getcwd() . '/' . $GLOBALS['drive'] . $_COOKIE[$GLOBALS['cookieName']] . '.git/';
    $gitLogContents;
    runCommandAsRoot('cd '.$homedir.';git log >/tmp/'.$GLOBALS['brandname'].$_COOKIE[$GLOBALS['cookieName']].'.zylog');
    $gitLogContents = file_get_contents('/tmp/'.$GLOBALS['brandname'].$_COOKIE[$GLOBALS['cookieName']].'.zylog');
    runCommandAsRoot('rm /tmp/'.$GLOBALS['brandname'].$_COOKIE[$GLOBALS['cookieName']].'.zylog');
    if ($makeLinks) {
        $gitLogContents = preg_replace('/commit(\s*)(.*)\nAuthor:/', 'commit\1<a href="viewlogfiles.php?c=\2">\2</a>'."\n".'Author:', $gitLogContents);
     }
        $gitLogContents = preg_replace('/\n/', '<br />', $gitLogContents);
    return $gitLogContents;
}

function displayChangedFilesInCommit($c) {
    $homedir = getcwd() . '/' . $GLOBALS['drive'] . $_COOKIE[$GLOBALS['cookieName']] . '.git/';
    runCommandAsRoot('cd '.$homedir.';git ls-tree --name-only -r '.$c.' >/tmp/'.$GLOBALS['brandname'].$_COOKIE[$GLOBALS['cookieName']].'.zylog');
    $displayChangedFiles = file_get_contents('/tmp/'.$GLOBALS['brandname'].$_COOKIE[$GLOBALS['cookieName']].'.zylog');
    runCommandAsRoot('rm /tmp/'.$GLOBALS['brandname'].$_COOKIE[$GLOBALS['cookieName']].'.zylog');
    return $displayChangedFiles;
}

function runCommandAsRoot($cmd) {
    $f = 'runthis.sh';
    $fh = fopen($f, 'w') or die ("can't open $f");  
    fwrite($fh, $cmd);  
    fclose($fh);
    system('./'.PHP_ROOT);
    unlink($f); 
}
