<?php
    $hostname = gethostname();
    $systemApps = shell_exec('echo \'{"action":"list","location":{"hostname":"' . $hostname . '"}}\' | /usr/bin/sudo /usr/libexec/nethserver/api/system-apps/read');
    header('Content-type: application/json');
    echo $systemApps;
?>
