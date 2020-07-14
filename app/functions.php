<?php

/**
 * This document is used to put the global functions
 */

use Model\Router;

/** @var $auth_enabled enable authentication ? */
$auth_enabled = true;
/**
 * Creates an error response based on code.
 * @param Int $code
 */
function setErrorCode($code)
{
    header('HTTP/1.0 ' . $code);
}

/**
 * Logs the ocurred error
 * @param $error is the array of code and message.
 */
function report($error)
{
    // $error['FROM_IP'] = ORIGIN_HTTP_ADDR;
    $error['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
    $error['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
    if (file_exists('app/logs/error.log')) {
        error_log(json_encode($error) . "\n", 3, 'app/logs/error.log');
    }
}

/**
 * Returns an Array error message based on the code.
 * Every message must be set in util/errors.php -- Reserve HTTP Codes.
 * @param Int $code the error code such as 404, 500, etc.
 * @return Array error code, its message and current time.
 */
function error_message(Int $code)
{
    $error = require_once('app/util/errors.php');
    setErrorCode($error[$code]['code']);
    report($error[$code]);
    return array_key_exists($code, $error) ? $error[$code] : $error[101];
}
/**
 * Gets the root host uri
 */
function getRootUri()
{
    return getenv('HTTP_HOST');
}

/**
 * Spits the requested content
 * @param Array $content is the formatted array to put on the response message
 */
function send(array $content)
{
    print_r(json_encode($content, JSON_INVALID_UTF8_IGNORE));
}

function authEnabled()
{
    global $auth_enabled;
    return $auth_enabled;
}

/**
 * Performs a try catch default request with PDO
 * @param  PDOStatement $request the PDO prepared statement
 * @return PDOStatement|Bool unfetched result or false
 */

function perform_query_pdo(PDOStatement $request, Bool $show_errors = false)
{
    try {
        if ($request->execute()) {
            return $request;
        }
        $show_errors ? print_r($request->errorInfo()) : null;
    } catch (\PDOException $e) {
        //
    }
    return false;
}
/**
 * Returns an Object Array or pure Array.
 * @param PDOStatement $q is an unfetched PDO statement result.
 * @param String $cls is the used Vendor/Class to append
 * @param Bool $map encodes the strings
 * @return Array 
 */
function make_array_from_query(PDOStatement $q, String $cls = null, Bool $map = false)
{
    $r = array();

    while ($ln = $q->fetch(PDO::FETCH_ASSOC)) {
        if ($cls == null && !$map) {
            $r[] = $ln;
        } elseif ($cls != null && $map) {
            $r[] = new $cls(array_map('utf8_encode', $ln));
        } else {
            $r[] = new $cls($ln);
        }
    }
    return $r;
}
/**
 * Checks if URL exists
 * @param String $url url to check
 */
function url_exists(String $url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($code == 200) {
        $status = true;
    } else {
        $status = false;
    }
    curl_close($ch);
    return $status;
}

/**
 * Swap accentuation to raw chars.
 * @param String $string string to swap
 */
function swapChars(String $string)
{
    return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
}

function unique_id(Int $size = 6, $hash = 'sha256')
{
    $d = time();
    $pre = 'unique_id_mm@@_';
    $pre = hash($hash, $pre . $d);
    $uid = '';
    $len = 6;

    if(!$pre) return ['res' => false, 'msg' => 'Invalid hashing algorithm.'];

    if ($size <= 128) {
        $len = $size;
    } else {
        return array('res' => 'Length must be an integer below 128!', 'err' => true);
    }
    for ($i = 0; $i < $len; $i++) {
        $uid .= substr($pre, rand(0, $len), 1);
    }

    return array('uid' => $uid, 'length' => strlen($uid), 'hash' => $pre);
}

/**
 * Sanitizes the string to prevent failures and security problems
 * @param String $name is the name of the param
 * @param Int $input_type is the request input type for filter 
 * @see filter_input() to more details.
 * @return String sanitized
 */
function sanitize_string(String $name, Int $input_type = INPUT_POST)
{
    if ($input_type == INPUT_POST) {
        $post = array_pop($_POST);
        $post = json_decode($post);
        return filter_var($post[$name], FILTER_SANITIZE_STRING);
    }
    return filter_var(filter_input($input_type, $name), FILTER_SANITIZE_STRING);
}

/**
 * Encodes a string
 * @param String $str
 */

function pop_encode(String $str)
{
    return base64_encode(strrev(base64_encode($str))) . '*nak()S-_=';
}

/**
 * Decodes a string
 * @param String $hash the encoded string
 */

function pop_decode(String $hash)
{
    $_s = preg_replace('/\*nak\(\)S\-\_\=/', '', $hash);
    return base64_decode(strrev(base64_decode($_s)));
}

/**
 * Encrypts a string
 * @param String $str the string
 */

function pop_encrypt(String $str)
{
    return hash('sha256', strrev(hash('sha256', base64_encode($str))));
}

/**
 * Gets the post variables or array
 * @param String $key is the key
 * @return mixed
 */

function get_post($key = false)
{
    return $_POST;
}

/**
 * Returns a system message based in a code
 * @param Int $errcode the error code came fro mthe database
 * @return String an error message
 */

function get_sysmsg(Int $msgCode)
{
    if (file_exists('app/System-messages.json')) {
        $err = file_get_contents('app/System-messages.json');
        $err = json_decode($err, true);
        return $err[$msgCode]['message'];
    }
    return 'We\'ve found an error.';
}

/**
 * Generates a secure password.
 * @param Int $length number of characters
 * @return String the password
 */
function pop_password(Int $length = 8)
{
    $charset = 'abcdefghijkSTUlmnopBCDqrstuvwxyz'
        . 'ABCDEFGHI#$%&JKLMNOPQRSTUVWXYZ'
        . '0123456789!@#$%&*()';
    $p = substr(str_shuffle(str_repeat($charset, $length)), 0, $length);
    return $p;
}

/**
 * Gets the part template for emails and others
 * @param String $template the template name
 * @return File|Bool the template or false if template file not found.
 */

function get_part_template(String $template, array $data)
{
    if (file_exists('app/util/templates/' . $template . '-template.php')) {
        $html = file_get_contents('app/util/templates/' . $template . '-template.php');
        foreach ($data as $key => $value) {
            $html = preg_replace("/\{\{" . $key . "}\}/", $value, $html);
        }
        return $html;
    }else{
        return false;
    }
}
