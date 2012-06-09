<?php
require_once('configuration.php');
require_once('conn.php');
function setup() {
    echo "Running setup becuase I cannot find something...<br /><br />";
    echo "Don't forget to run <code>runme.sh</code> script as root by running <code>sh runme.sh</code>!<br />If any errors occur, cd to " . getcwd() . " and, as root, type the following verbatim: <code>cd ../; chmod 777 -R bakmiup; chown -R http:http bakmiup; cd " . getcwd() . ";</code><br /><br />";
    serverSetup();
    echo "Setting up alien updater...<br />";
    updaterSetup();
    echo 'Setup database...<br />';
    mysql_query('CREATE DATABASE IF NOT EXISTS ' . $GLOBALS['mysql_database']);
    echo 'Setting up users table...<br />';
    mysql_select_db($GLOBALS['mysql_database']); 
    setupTable("userTable");
    echo "&nbsp;&nbsp;&nbsp;&nbsp;...done.";
    die;
}

function updaterSetup() {
$f = 'update.pl';
$fh = fopen($f, 'w') or die ("can't open $f");
$file = <<<'CONTENT'
#!/usr/bin/perl
use strict;
use warnings;
use LWP::Simple;
use LWP::UserAgent;

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
        my $dir = `unzip update.zip`;
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
    }
}
CONTENT;
fwrite($fh, $file);
fclose($fh);
$f = "runme.sh";
$fh = fopen($f, 'a') or die ("can't open $f");
fwrite($fh, 'crontab -l >updatercron; echo "0 0 * * * cd ' . getcwd() . ';perl update.pl">>updatercron; crontab updatercron; rm updatercron $0;');
fclose($fh);
}

function serverSetup() {
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
    $f = 'runme.sh';
    $fh = fopen($f, 'w') or die ("can't open $f");
    fwrite($fh, 'groupadd ' . $GLOBALS['linuxGroup'] . ';');
    fwrite($fh, 'gcc wrapper.c -o php_root;chown root php_root;chmod u=rwx,go=xr,+s php_root; rm wrapper.c;');
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
    $result = @mysql_query($sql);
    if (!$result) {
        $sql = 'CREATE TABLE `' . $GLOBALS['userTable'] . '` (
               `userid` bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
               `username` longtext NOT NULL,
               `proof` longtext NOT NULL
               ) COMMENT="";';

        mysql_query($sql) or die(mysql_error());
    }
}

function setupGit($repoName) {
    if (!(file_exists(getcwd() . '/' . $GLOBALS['drive'] . $repoName . '.git'))) {
        system("mkdir " . getcwd() . '/' . $GLOBALS['drive'] . $repoName . '.git');
    }
    system("mkdir " . getcwd() . '/' . $GLOBALS['drive'] . $repoName . '.git');
    runCommandAsRoot('cd ' . getcwd() . '/' . $GLOBALS['drive'] . $repoName . '.git; git init');
    runCommandAsRoot('chown -R ' . $repoName . ':' . $GLOBALS['linuxGroup'] . ' ' . getcwd() . '/' . $GLOBALS['drive'] . $repoName . '.git');
    return true;
}

function setupSSH($u) {
    runCommandAsRoot(' su - ' . $u . ' -c " mkdir -p ~/.ssh;  ssh-keygen -q -t rsa -N ' . "''" . '  -f ~/.ssh/id_rsa;"');
}

function generateMac($exclusionArray) {
    runCommandAsRoot('mkdir /tmp/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '; chmod 777 /tmp' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '; chown http:http ' . $GLOBALS['linuxGroup'] . ' /tmp/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']]);
    $original_location = getcwd(); 
    runCommandAsRoot('chmod 0755 /tmp/'.$GLOBALS['brandname'].$_COOKIE[$GLOBALS['cookieName']]); 
    chdir('/tmp/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']]);  
    $f = 'writeit.sh';
    $fh = fopen($f, 'w') or die ("can't open $f"); 
    fwrite($fh, '#!/bin/bash' . "\n");
    fwrite($fh, 'servername='.$GLOBALS['brandname'].";\n");
    fwrite($fh, 'shfile=${servername}_setup.sh;'."\n");   
    fwrite($fh, 'username='.$_COOKIE[$GLOBALS['cookieName']].";\n");  
    fwrite($fh, 'server='.$GLOBALS['server'].";\n");  
    fwrite($fh, 'port='.$GLOBALS['port'].";\n");  
    $rest = <<<'END_REST'
original_path=$(pwd);
cd ~;
home_path=$(pwd);
mkdir -p $HOME/.ssh;

END_REST;
if (!empty($exclusionArray)) {
    foreach ($exclusionArray as $val) {
        $rest .= "echo '$val' >> .gitignore\n";
    }
}
$rest .= <<<'END_REST'
mkdir ${servername}_$username;
cd ${servername}_$username;
echo cd $home_path >$shfile
echo scp -P $port -rv . ${username}@${server}: >>$shfile
echo 'if [ ! -f $HOME/.ssh/zysys_bakup_rsa ]; then
         ssh-keygen -t rsa -N "" -q -f ~/.ssh/zysys_bakup_rsa
      fi' >>$shfile
echo "cat $HOME/.ssh/zysys_bakup_rsa.pub | ssh ${username}@${server} -p $port 'cat >> .ssh/authorized_keys'" >>$shfile
echo "ssh-add $HOME/.ssh/zysys_bakup_rsa" >>$shfile
echo "if [ -f $HOME/.gitignore ]; then
      cat $HOME/.gitignore | ssh ${username}@${server} -p $port 'cat >> .gitignore'
      fi" >>$shfile
echo "cat ~/.ssh/zysys_bakup_rsa.pub | ssh ${username}@${server} -p $port 'cat >> .ssh/authorized_keys'" >>$shfile
echo "ssh-add $HOME/.ssh/zysys_bakup_rsa" >>$shfile 
echo git init >>$shfile
echo 'git add . -v' >>$shfile;
echo 'git commit -am "Initial Commit."' >>$shfile;
echo ssh ${username}@${server} -p $port "'git init; git add . -v; git commit -am" '"Initial Commit."'"'" >>$shfile
echo 'crontab -l > bakup;' >>$shfile;
echo "echo $[ ( $RANDOM % 59 )  + 1 ] '* * * * /bin/bash ${home_path}/${servername}_${username}/run.sh '>>bakup;" >>$shfile;
echo 'crontab bakup' >>$shfile;
echo 'rm bakup' >>$shfile;
echo 'path=$(pwd)' >run.sh
echo cd $home_path >>run.sh
echo git add . -v >>run.sh
echo 'git commit -am "Backup for: `date`"' >>run.sh
echo "cat" '`git format-patch master -1 --suffix=.'${servername}'_patch`' "| ssh ${username}@${server} -p $port ""'cat > currPatch.'${servername}.'_patch; git apply currPatch.'.${servername}'_patch; rm -R *.'${servername}'_patch;'" >>run.sh
echo 'cd $path' >>run.sh 
sh $shfile;
rm $0;
END_REST;
    fwrite($fh, $rest);
    fclose($fh);
    chdir($original_location);
    runCommandAsRoot('chmod -R 755 /tmp/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '; chown -R ' . $_COOKIE[$GLOBALS['cookieName']] . ':' . $GLOBALS['linuxGroup'] . $GLOBALS['linuxGroup'] . ' /tmp/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']]); 
    runCommandAsRoot('cd /tmp/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '; mkdir -p ' . getcwd() . '/download/;  zip ' . getcwd() . '/download/' . $GLOBALS['brandname'] . $_COOKIE[$GLOBALS['cookieName']] . '.zip ./* ');
    return true;
}

function runCommandAsRoot($cmd) {
    $f = 'runthis.sh';
    $fh = fopen($f, 'w') or die ("can't open $f");  
    fwrite($fh, $cmd);  
    fclose($fh);
    system('./php_root');
    unlink($f); 
}
?>
