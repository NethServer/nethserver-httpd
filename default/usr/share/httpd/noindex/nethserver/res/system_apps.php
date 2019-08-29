<?php
    header('Content-type: application/json');
    $cockpitPath = "/usr/share/cockpit/";
    $cockpitInstalled = file_exists($cockpitPath);

    if (!$cockpitInstalled) {
        http_response_code(404);
    } else {
        $systemApps = shell_exec('echo \'{"action":"list","location":{"hostname":"' . $_SERVER['SERVER_NAME'] . '"}}\' | /usr/bin/sudo /usr/libexec/nethserver/api/system-apps/read');
        $systemAppsJson = json_decode($systemApps, true);
    
        foreach ($systemAppsJson as &$app) {
            $iconUrl = $cockpitPath . $app['id'] . '/' . $app['icon'];
            $iconData = file_get_contents($iconUrl);
            $app['iconBase64'] = base64_encode($iconData);
        }
        $systemApps = json_encode($systemAppsJson);
        echo $systemApps;
    }
