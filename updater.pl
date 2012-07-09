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
