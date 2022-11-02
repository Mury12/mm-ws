<?php

/**
 * Routes are now defined separately in src/routers/router-file.php
 * Do not change this file to avoid route breaking.
 * This file is used only to define route domains such as ws->v1|v2|v3
 * and so on
 */

use MMWS\Factory\EndpointFactory;

/**
 * This loads all the routers in the src/routers.
 * @param string[] $directory the directory
 */
function router_loadupload($directory = 'src/routers'): array
{
    $router = [];
    if ($handle = opendir($directory)) {
        while ($file = readdir($handle)) {
            if ($file == "." || $file == "..") continue;
            else {
                $pathname = $directory . '/' . $file;
                $domain = str_replace('.php', '', $file);
                if (is_dir($pathname)) {
                    $loadedRoutes = router_loadupload($pathname);
                } else {
                    $loadedRoutes = require_once $pathname;
                }
                if(array_key_exists($domain, $router)){
                    $router[$domain] = array_merge($router[$domain], $loadedRoutes);
                }else{
                    $router[$domain] = $loadedRoutes;
                }
            }
        }
    }
    return $router;
}

return router_loadupload();
