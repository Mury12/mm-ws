<?php

namespace MMWS\HTTP;

use Exception;

/**
 * Makes http requests
 * @param Array $conf configuration array with indexes:
 * @var uri is the target address
 * @var method is the method POST or GET, default POST
 * @var headers is the header format default multipart/form-data
 */
class CURLRequestHandler
{

    private $uri = 'https://localhost/ws/v2/';
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

    function send(Bool $show_errors = false)
    {
        $ctx = stream_context_create(['http' => $this->conf]);
        $res = file_get_contents($this->uri, false, $ctx);

        if (!$res) return ['res' => false, 'msg' => 'Request result is null.'];

        $res = json_decode($res, true);

        if ($show_errors) {
            print_r(json_last_error_msg());
        }

        return $res ?? false;
    }
}
