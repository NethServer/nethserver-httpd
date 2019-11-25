#!/usr/bin/perl

#
# Copyright (C) 2019 Nethesis S.r.l.
# http://www.nethesis.it - nethserver@nethesis.it
#
# This script is part of NethServer.
#
# NethServer is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License,
# or any later version.
#
# NethServer is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with NethServer.  If not, see COPYING.
#

use strict;
use warnings;
use JSON;


sub list_features
{
    my $features = {
        dashboard => { installed => JSON::true, packages => ['nethserver-httpd'] },
        virtualhosts => { installed => (-f '/etc/e-smith/db/vhosts/defaults/default/type') ? JSON::true : JSON::false, packages => ['nethserver-httpd-virtualhosts'] },
        'rh-php71-php-fpm' => { installed => (-f '/etc/e-smith/db/configuration/defaults/rh-php71-php-fpm/type') ? JSON::true : JSON::false, packages => ['nethserver-rh-php71-php-fpm'] },
        'rh-php72-php-fpm' => { installed => (-f '/etc/e-smith/db/configuration/defaults/rh-php72-php-fpm/type') ? JSON::true : JSON::false, packages => ['nethserver-rh-php72-php-fpm'] },
        ftp => { installed => (-f '/etc/e-smith/db/configuration/defaults/vsftpd/type') ? JSON::true : JSON::false, packages => ['nethserver-vsftpd'] },
        logs => { installed => JSON::true, packages => ['nethserver-httpd'] },
        about => { installed => JSON::true, packages => ['nethserver-httpd'] },
    };

    return $features;
}

1;
