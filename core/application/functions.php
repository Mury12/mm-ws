<?php

/**
 * This document is used to put the global functions
 */

use MMWS\Factory\RequestExceptionFactory;
use MMWS\Abstracts\Model;

/** @var $auth_enabled enable authentication ? */
$auth_enabled = true;
/**
 * Creates an http response based on code.
 * @param Int $code
 */
function set_http_code($code)
{
    header('HTTP/1.1 ' . $code);
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
    if (!file_exists('src/logs/error.log')) {
        file_put_contents('src/logs/error.log', '');
    }
    error_log(json_encode($error) . "\n", 3, 'src/logs/error.log');
}

/**
 * Returns an http message based on the code.
 * Every message must be set in util/errors.php -- Reserve HTTP Codes.
 * @param Int $code the error code such as 404, 500, etc.
 * @param mixed $status can be either a string or an array and will be shown as status.
 * @return Array error code, its message and current time.
 */
function http_message(Int $code, $status = null)
{
    $error = $error ?? require('src/util/errors.php');
    set_http_code($error[$code]['code']);
    if ($status) {
        $error[$code]['status'] = $status;
    }
    report($error[$code]);
    return array_key_exists($code, $error) ? $error[$code] : $error[101];
}
/**
 * Gets the root host uri
 */
function get_root_uri()
{
    return getenv('HTTP_HOST');
}

function modelToArray($object)
{
    if ($object instanceof Model) {
        $model = $object->toArray([], false);
        return array_map(function ($prop) {
            if ($prop instanceof Model) return modelToArray($prop);
            return $prop;
        }, $model);
    }
    return $object;
}
/**
 * Spits the requested content
 * @param Array $content is the formatted array to put on the response message
 */
function send($content)
{
    if (!$content || is_array($content) && !sizeof($content)) {
        set_http_code(204);
        return;
    }

    if (is_array($content)) {
        $response = array_map(function ($item) {
            return modelToArray($item);
        }, $content);
    } else $response = modelToArray($content);

    print_r(json_encode($response, JSON_INVALID_UTF8_IGNORE));
    return;
}
function auth_enabled()
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
        if ($show_errors) {
            print_r($request->errorInfo());
            $request->debugDumpParams();
        }
    } catch (\PDOException $e) {
        throw $e;
    }
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
function swap_chars(String $string)
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

    if (!$pre) return ['res' => false, 'message' => 'Invalid hashing algorithm.'];

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
function post_params($key = false)
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
        $_POST = json_decode(file_get_contents('php://input'), true);
        return $_POST;
    }
}

/**
 * Gets the patch variables or array
 * @param String $key is the key
 * @return mixed
 */
function patch_params($key = false)
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) === 'PATCH') {
        $_PATCH = json_decode(file_get_contents('php://input'), true);
        return $_PATCH;
    }
}

/**
 * Gets the patch variables or array
 * @param String $key is the key
 * @return mixed
 */
function put_params($key = false)
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) === 'PUT') {
        $_PUT = json_decode(file_get_contents('php://input'), true);
        return $_PUT;
    }
}
/**
 * Returns a system message based in a code
 * @param Int $errcode the error code came fro mthe database
 * @return String an error message
 */
function get_sysmsg(Int $msgCode)
{
    if (file_exists('src/System-messages.json')) {
        $err = file_get_contents('src/System-messages.json');
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
    if (file_exists('src/util/templates/' . $template . '-template.php')) {
        $html = file_get_contents('src/util/templates/' . $template . '-template.php');
        foreach ($data as $key => $value) {
            $html = preg_replace("/\{\{" . $key . "}\}/", $value, $html);
        }
        return $html;
    } else {
        return false;
    }
}

/**
 * Checks if a string contains any of the substrings given
 * @param string $source source string to check
 * @param array $cmp array of strings to compare
 * @param string $regEx set true if cmp is an unescaped regex array.
 * @param bool $getMatches return the matched therms.
 * @return bool|array if found any of the therms.
 */
function str_in(string $source, array $cmp, bool $regEx = false, bool $getMatches = false)
{
    $matches = array();

    if (!$regEx) {
        foreach ($cmp as $therm) {
            if (strpos(strtolower($source), strtolower($therm), 0) !== false) {
                if ($getMatches) {
                    array_push($matches, $therm);
                } else {
                    return true;
                }
            }
        }
        return sizeof($matches) > 0 ? $matches : false;
    }
}

/**
 * Changes the snake_case to camelCase from an array
 * @param String $var variable name
 */
function snake_to_camel($content, Bool $capitalize = false)
{
    if (is_array($content)) {
        $output = array();
        /** Loops through the array to get the keys */
        foreach ($content as $key => $value) {
            $parts = explode('_', $key);

            $outVarName = '';
            /** Loops through every name part separated by underscore (_) */
            $i = 0;
            foreach ($parts as $part) {
                if ($i === 0 && !$capitalize) {
                    $outVarName = strtolower($part);
                    $i++;
                    continue;
                }
                $outVarName .= ucfirst(strtolower($part));
                $i++;
            }
            $output[$outVarName] = $value;
        }
        return $output;
    }

    $parts = explode('_', $content);

    $outVarName = '';
    /** Loops through every name part separated by underscore (_) */
    $i = 0;
    foreach ($parts as $part) {
        if ($i === 0 && !$capitalize) {
            $outVarName = strtolower($part);
            $i++;
            continue;
        }
        $outVarName .= ucfirst(strtolower($part));
        $i++;
    }
    return $outVarName;
}
/**
 * Checks if the data given matches with the given keys (props)
 * @param array $data the data to verify
 * @param string[] $keys array of keys to validate
 * @param string $instanceOf class to instantiate and check for properties
 * @return string[]|false Array of field errors or false if no errors.
 */
function keys_match($data, array $keys, string $instanceOf = null)
{
    $errors = [];
    foreach ($keys as $key => $value) {
        if ($instanceOf && $data instanceof $instanceOf) {
            if (!property_exists($data, $value) || $data->{$value} === null || $data->{$value} === '') {
                $errors[] = $value;
            }
        } else if (!array_key_exists($value, $data) || $data[$value] === null || $data[$value] === '') {
            $errors[] = $value;
        }
    }
    return sizeof($errors) ? $errors : false;
}

/**
 * Remove null values or empty arrays from an array
 * @param array &$array subject
 * @return array
 */
function remove_nulls(array &$array)
{
    foreach ($array as $key => $prop) {
        if (is_array($prop) && !sizeof($prop)) {
            unset($array[$key]);
        } else {
            if (!$prop) unset($array[$key]);
        }
    }
}
