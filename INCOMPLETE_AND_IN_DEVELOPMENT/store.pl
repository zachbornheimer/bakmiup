#!/usr/bin/perl
use strict;
use warnings;
require File::stat;
require Time::localtime;
use Carp;
use Cwd;
use File::Find;

if ($#ARGV != 0) {
    croak "Invalid number of arguments.\n";
}

if ($ARGV[0] !~ /^\d*$/) {
    croak "Invalid memory limit.\n";
}

my $memoryLimit = $ARGV[0];

chdir ("$ENV{HOME}");
my @appendToIgnoreDirectories = ('.', '..', '...', '.git');
my @ignoreDirectories = ();
my @ignoreFiles = ();
open(IGNORE, ".gitignore");
while (<IGNORE>) {
    chomp($_);
    my $f = $_;
    if ($_ =~ /\//) {
        $ignoreDirectories[++$#ignoreDirectories] = $f;
} else {
    $ignoreFiles[++$#ignoreFiles] = $f;
    push (@ignoreFiles, $_);
}
}
close(IGNORE);

@ignoreDirectories = uniq(@ignoreDirectories);
@ignoreFiles = uniq(@ignoreFiles);
@ignoreDirectories = map {$_ =~ s/[\n\r]$//; $_;} @ignoreDirectories;
@ignoreFiles = map {$_ =~ s/[\n\r]$//; $_;} @ignoreFiles;

open (IGNORE, ">.gitignore");
print IGNORE join("\n", @ignoreDirectories)."\n";
print IGNORE join("\n", @ignoreFiles);
close (IGNORE);
push (@ignoreDirectories, @appendToIgnoreDirectories);

my $ssh;
my $scp;
open (STORESETTINGS, ".settings.zy");# or die "Program Settings not defined in ~/.settings.zy"; 
while (<STORESETTINGS>) {
    if ($_ =~ /^ssh:/) {
        chomp($_);
        $ssh = $_;
    }

    if ($_ =~ /^scp:/) {
        chomp($_);
        $scp = $_;
    }
}

print "Scanning...";
find({wanted => \&go, no_chdir => 1}, ".");
print "Done Scanning...";

my $i;
sub go {
    my @modified;
    if (!(elementInArray($_, @ignoreDirectories)) && $_ ne '.') {
        $i++;
        if ((-s $_) >= $memoryLimit) {
            if (!elementInArray($_, @ignoreFiles)) {
# print "Name: " . $_ . ", Size: " . (-s $_) ."\n";
                @modified = storeIfNecessary($_);
            }
        }
    }
    foreach (@modified) {
        my @abbr = qw( Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec );
        my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);        
        my $backupDate = $abbr[$mon].$mday.($year+1900);
        print $backupDate;exit;
# Each of these are modified.
        #system($ssh . " 'mkdir -p overflow; cd overflow; mdkir -p originals; mkdir -p $backupDate;' ");
        #system('scp ' . $_ . ' ' . $scp . ':'.$backupDate.'/');
# Discerne how to escape ", ', and spaces...following line:
         my $escapedFileName = $_;
         my $escapedFileName =~ s/(["' ])/\\$1/g;
         print $escapedFileName;
#        system($ssh . " 'if ( [ -d \='`" . $_ . "
    }
}

sub elementInArray {
    my $element = shift;
    my @array = @_;
    chomp($element);
    $element =~ s/^\.\//\//;
    @array = map {$_ =~ s/\./\\\./; $_=~s/\*/\.\*/; $_;} @array;
    @array = map {$_ =~ s/\/$//; $_;} @array;
    foreach (@array) {
        if ($element =~ /\/$_/) {
            return 1;
        }
    }
    return 0;
}


sub uniq {
    my %h;
    return grep { !$h{$_}++ } @_
}

sub storeIfNecessary {
    my $fullPath = shift;
    use File::stat;
    use Time::localtime;
    my $timestamp = stat($fullPath)->mtime;
    my $write = 0;
    my @data = ();
    my @returnPaths = ();
    if (-e ".largedb.zy") {
        open (DB, ".largedb.zy");
        while (<DB>) {
            chomp($_);
            my @line = split (/\.\//, $_);
            my $oldTimestamp = shift(@line);
            $_ = join('./', @line);
            $_ = './' . $_;
            if ($_ eq $fullPath) {
                $write = 1;
                if ($timestamp > $oldTimestamp) {
                    push (@data, $timestamp.$fullPath);
                    push (@returnPaths, $fullPath);
                } else {
                    push (@data, $oldTimestamp.$_);
                }
            } else {
                push (@data, $oldTimestamp.$_);
            }
        }
        if (!$write) {
            $write = 1;
            push (@data, $timestamp . $fullPath);
            push (@returnPaths, $fullPath);
        }
    } else {
        $write = 1;
        push (@data, $timestamp.$fullPath);
        push (@returnPaths, $fullPath);
    }

    @data = map {$_ =~ s/[\n\r]$//; $_;} @data;
    @data = uniq(@data);
    if ($write) {
        open (DBW, ">.largedb.zy");
        print DBW join("\n", @data);
        close DBW;
    }
    return @returnPaths;
}
