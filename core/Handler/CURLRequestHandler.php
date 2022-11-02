<?php

namespace MMWS\Handler;

use Exception;

/**
 * Makes CURL HTTP requests
 * 
 * @param Array $conf configuration array with indexes:
 * @param uri is the target address
 * @param method is the method POST or GET, default POST
 * @param headers is the header format default multipart/form-data
 * 
 * ----------
 * 
 * Example usage:
 * 
 * use MMWS\Handler\CURLRequestHandler;
 * 
 * $conf = array(
 * 
 *      'method' => 'GET'
 * 
 *      'header' => 'Authorization: AUTH_TOKEN, x-extra-headers: CONTENT',
 * 
 *      'content' => array(
 * 
 *          'username' => 'Garry',
 * 
 *          'password' => 'Ms&654$.@@'
 *          
 *      )
 * );
 * 
 * $curl = new CURLRequestHandler($conf);
 * 
 * $curl->send();
 * 
 * if($curl) {...}
 * 
 * ----------
 * @package MMWS
 * @author Andre Mury <mury_gh@hotmail.com>
 * @version MMWS^0.9.1-alpha
 * 
 */
class CURLRequestHandler
{

    /**
     * @var String $uri the URL to request
     */
    private $uri = 'https://localhost/ws/v2/';

    /**
     * @var Array<String> $conf the configuration to make the request
     */
    private $conf = [
        'method' => "GET",
        'header' => 'Authorization: 52F58-B6E7A-25821-E57B7-69431-6EA4-95155-D45C9-E617A',
        'content' => [],
    ];

    function __construct(array $conf)
    {
        if (array_key_exists('uri', $conf)) {
            $this->uri .= $conf['uri'];
            unset($conf['uri']);
            foreach ($conf as $c => $value) {
                $this->conf[$c] = $value;
                $c == 'content' ? $this->conf[$c] = http_build_query($value) : null;
            }
        } else {
            throw new Exception('An URL must be provided.', 500);
        }
    }

    /**
     * Performs the request
     * @param Bool $show_errors prints JSON errors
     * 
     * @return Array|false the request result or false if not succeed.
     */
    function send(Bool $show_errors = false)
    {
        $ctx = stream_context_create(['http' => $this->conf]);
        $res = file_get_contents($this->uri, false, $ctx);

        if (!$res) return ['res' => false, 'message' => 'Request result is null.'];

        $res = json_decode($res, true);

        if ($show_errors) {
            print_r(json_last_error_msg());
        }

        return $res ?? false;
    }
}
