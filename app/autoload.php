<?php

/**
 * This file loads all the class files. 
 */

/** Global functions*/
require_once 'functions.php';

/**
 * This loads all the classes in the app/partials/class/  subfolders.
 * @var DirectoryIterator $dir 
 */
$dir[] = new DirectoryIterator(dirname('app/partials/_core/index.php'));
$dir[] = new DirectoryIterator(dirname('app/partials/classes/index.php'));
foreach ($dir as $directory) {
    foreach ($directory as $fileinfo) {
        if (
            $fileinfo->getFilename() == "."  ||
            $fileinfo->getFilename() == ".." ||
            $fileinfo->getType() == "file"
        )
            continue;
        else {
            if ($handle = opendir($fileinfo->getPathname()))
                while ($file = readdir($handle)) {
                    if ($file == "." || $file == "..") continue;
                    else {
                        require_once $fileinfo->getPathname() . '/' . $file;
                    }
                }
        }
    }
}
