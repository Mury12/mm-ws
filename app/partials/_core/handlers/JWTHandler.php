<?php

namespace MMWS\Handler;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use MMWS\Model\User;

/**
 * Manages JWT to authenticate users and
 * permit requests to be done
 */
class JWTHandler
{
    /**
     * Creates and delegates a JWT 
     * @param User $user
     * @return string token
     */
    public static function create(User $user)
    {
        if (!_JWT_DEFINED_KEY_)
            throw new \Error('Tries JWT assignment but the security key is not defined.');
        if (!$user || !$user->id)
            throw new \ParseError("Can't create a token without User uuid.");

        $key = _JWT_DEFINED_KEY_;
        $exp = new \DateTime();
        $exp->add(new \DateInterval('P7D'));

        $payload['iss'] = $_SERVER['REMOTE_ADDR'];
        $payload['sub'] = $user->id;
        $payload['exp'] = $exp->getTimestamp();
        $payload['nbf'] = (new \DateTime())->format('yy-m-d H:m:s');
        $payload['iat'] = (new \DateTime())->format('yy-m-d H:m:s');
        $payload['jti'] = unique_id(12)['uid'];

        $jwt = JWT::encode($payload, $key);
        SESSION::add('@app:jwt', $jwt);
        setcookie('app-token', $jwt, $exp->getTimestamp());
        return $jwt;
    }
    /**
     * Verify a JWT token
     * @param string $jwt authorization token
     * 
     * @return boolean
     */
    public static function verify($jwt)
    {
        if ($jwt === '' || !$jwt)
            return false;
        try {
            $decoded = JWT::decode($jwt, _JWT_DEFINED_KEY_, ['HS256']);
            SESSION::add('user_id', $decoded->sub);
            return true;
        } catch (ExpiredException $e) {
            return false;
        }
    }
}
