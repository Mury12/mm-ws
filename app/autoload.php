<?php

/**
 * This file loads all the class files. 
 */

/** Global functions*/
require_once 'functions.php';


/**
 * This loads all the classes in the app/partials/class/  subfolders.
 * @var string[] $dir 
 */
$dir[] = 'app/partials/_core/';
$dir[] = 'app/partials/classes/';

foreach ($dir as $directory) {
    $folders = scandir($directory);
    foreach ($folders as $folder) {
        if (!preg_match('/(\.\.)|(\.)|(.\.php)/im', $folder)) {
            if ($handle = opendir($directory . $folder)) {
                while ($file = readdir($handle)) {
                    if ($file == "." || $file == "..") continue;
                    else {
                        $pathname = $directory . $folder . '/' . $file;
                        require_once $pathname;
                    }
                }
            }
        }
    }
}
