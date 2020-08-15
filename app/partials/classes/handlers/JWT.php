<?php

namespace MMWS\Handler;

use DateInterval;
use DateTime;

class JWT
{
    /**
     * @var String $JWT generated token
     */
    private $JWT;

    /**
     * @var Array $headers JWT headers
     */
    private $headers = array(
        'alg' => 'HS256',
        'typ' => 'JWT',
    );

    /**
     * @var Array $payload JWT body values
     * 
     * ---------
     * Indexes:
     * 
     * iss => application domain default $_SERVER['REMOTE_HOST']
     * 
     * sub => Token subject default SESSION::get('userID')
     * 
     * aud => who is able to use the token
     * 
     * exp => expiration date default date_add(new DateTime, new DateInterval('P1D'));
     * 
     * nbf => not accept before this date
     * 
     * iat => created date
     * 
     * jti => token id
     */
    private $payload = array(
        'iss' => '',
        'sub' => '',
        'aud' => '',
        'exp' => '',
        'nbf' => '',
        'iat' => '',
        'jti' => ''
    );

    public $jwt;

    function __construct(array $payload = null, array $headers = null)
    {
        if ($headers) $this->headers = $headers;

        $date = new DateTime();
        $date->add(new DateInterval('P1D'));

        /** Payload definition */
        $this->payload['iss'] = $payload['iss'] ?? $_SERVER['REMOTE_HOST'];
        $this->payload['sub'] = $payload['sub'] ?? SESSION::get('userID');
        $this->payload['aud'] = $payload['aud'] ?? '';
        $this->payload['exp'] = $payload['exp'] ?? $date->format('yy-m-d H:m:s');
        $this->payload['nbf'] = $payload['nbf'] ?? (new DateTime())->format('yy-m-d H:m:s');
        $this->payload['iat'] = $payload['iat'] ?? (new DateTime())->format('yy-m-d H:m:s');
        $this->payload['jti'] = $payload['jti'] ?? unique_id(12);

        $this->jwt = $this->encode();
    }

    private function encode()
    {
        $headers   = $this->parse($this->headers);
        $payload   = $this->parse($this->payload);
        $signature = hash_hmac('sha256', $headers . $payload, _JWT_PASSPHRASE_ ?? 'jwt_passphrase');

        return $headers.".".$payload.".".$signature;
    }

    private function parse(array $arr)
    {
        return str_replace('=','',base64_encode(json_encode(($arr))));
    }

}
