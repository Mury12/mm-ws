<?php

namespace MMWS\Handler;

/**
 * Handler file operations.
 * @param String $domain the folder domain
 * @param String filename the name for the file
 * 
 * -------------
 * 
 * Example Usage:
 * 
 * use MMWS\Handler\FileHandler;
 * 
 * $fh = new FileHandler('pictures', 'no_user.png');
 * 
 * $fh->getFile();
 * 
 * $fh = new FileHandler('pictures');
 * 
 * $fh->writeFile('file_path_or_content');
 * 
 * -------------
 * @package MMWS
 * @author Andre Mury <mury_gh@hotmail.com>
 * @version MMWS^0.6.1-alpha
 */
class FileHandler
{
    private $domain;
    public $filename;
    public $path;
    public $ext;
    public $file;

    function __construct(String $domain, String $filename = null)
    {
        $this->domain = $domain;
        $this->filename = $filename ?? null;
    }

    /**
     * Gets a file using given domain and filename
     * @return String file contents
     */
    function getFile()
    {
        $path = DEFAULT_FILE_PATH . $this->domain . $this->domain . '_' . $this->filename;
        return file_get_contents($path);
    }

    /**
     * Writes a file based in the input params
     * @param $file the file itself
     * @return Bool|String False if unsuccessful or the filename
     */
    function writeFile($file)
    {
        $this->file = $file;
        $file = file_get_contents(str_replace(' ', '+', $this->file));
        $parts = explode(',', $this->file);
        $ext = explode(';', explode('/', $parts[0])[1])[0];


        $filename =  $this->domain . '_' . unique_id(10)['hash'] . '.' . $ext;
        $path = DEFAULT_FILE_PATH . $this->domain . '/' . $filename;
        if (file_put_contents($path, $file)) {
            $this->path = $path;
            $this->filename = $filename;
            $this->ext = $ext;

            return $filename;
        }
        return false;
    }

    /**
     * Removes a file from a path
     * @param String $path the path for the file given in input params
     */
    function removeFile($path = null)
    {
        $f = $path ?? $this->path;
        if (file_exists($f)) {
            unlink($f);
        }
    }
}
