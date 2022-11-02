<?php

namespace MMWS\Interfaces;

use MMWS\Handler\Request;

/**
 * Middleware Interface
 * 
 * Every middleware built MUST implement this interface
 * 
 * ----------
 * 
 * Example Usage:
 * ```php
 * use MMWS\Interfaces\Middleware;
 * 
 * class Authentication implements Middleware
 * 
 * {
 *   function action(){ 
 *      // action code 
 *   }
 *   function init(){
 *     // init code
 *   }
 * }
 * ```
 * ----------
 * @package MMWS
 * @author Andre Mury <mury_gh@hotmail.com>
 * @version MMWS^0.6.1-alpha
 */
interface Middleware
{
    /**
     * The action that will be called by initializer
     * This method does all the procedures that the
     * middleware should do.
     */
    function action();

    /**
     * Initializes the middleware. It can be called inside
     * a constructor if wanted.
     */
    function init(Request $request);
}
