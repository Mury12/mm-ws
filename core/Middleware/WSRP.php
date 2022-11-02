<?php

namespace MMWS\Middleware;

use MMWS\Handler\Request;
use MMWS\Interfaces\Middleware;
// require_once(explode('src/', __DIR__)[0].'src/classes/interfaces/Middleware.php');

class WSRP implements Middleware
{
    function __construct()
    {
        //
    }

    private function SmithThis()
    {
        error_log('notdefined');
        if (!defined('_WILL_IT_SMITH_')) {
            $this->WillSmithMemesRevenge();
        }
    }

    private function WillSmithMemesRevenge()
    {
        $d = opendir($_SERVER['DOCUMENT_ROOT'] . '/publique');
        $c = array();
        while ($a = readdir($d)) {
            if ($a == ".." || $a == ".")
                continue;
            else
                array_push($c, $a);
        }
        $memeFile = $this->getRandomMeme($c);
        header('Location: /wsrp/' . $memeFile);
    }

    private function getRandomMeme(array $WillSmithMemeArrayFiles)
    {
        $memeArraySize = sizeof($WillSmithMemeArrayFiles) - 1;
        $memeRandomIndex = rand(0, $memeArraySize);
        $memeFile = $WillSmithMemeArrayFiles[$memeRandomIndex];
        return $memeFile;
    }

    function init(Request $request)
    {
        $this->action();
    }

    function action()
    {
        $this->SmithThis();
    }
}
