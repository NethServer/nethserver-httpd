<?php
    $cockpitPath = "/usr/share/cockpit/";
    $cockpitCmd = "/usr/libexec/nethserver/api/system-apps/read";
    $cockpitInstalled = file_exists($cockpitCmd);

    if (!$cockpitInstalled) {
        http_response_code(404);
    } else {
        header('Content-type: application/json');

        $systemApps = shell_exec('echo \'{"action":"launcher","location":{"hostname":"' . $_SERVER['SERVER_NAME'] . '", "protocol":"https:"}}\' | /usr/bin/sudo ' . $cockpitCmd);
        $systemAppsJson = json_decode($systemApps, true);

        if (is_null($systemAppsJson)) {
            // invalid json
            echo json_encode([]);
        } else {
            foreach ($systemAppsJson as &$app) {
                if (!array_key_exists('icon', $app)) {
                    $app['icon'] = 'logo.png';
                }
                $iconUrl = $cockpitPath . $app['id'] . '/' . $app['icon'];
                $iconData = file_get_contents($iconUrl);
                $app['iconBase64'] = base64_encode($iconData);
            }
            $systemApps = json_encode($systemAppsJson);
            echo $systemApps;
        }
    }
