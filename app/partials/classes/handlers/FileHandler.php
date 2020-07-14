<?php

namespace MMWS\Handler;

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

    function getFile()
    {
        $path = DEFAULT_FILE_PATH . $this->domain . $this->domain . '_' . $this->filename;
        return file_get_contents($path);
    }

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

    function removeFile($path = null)
    {
        $f = $path ?? $this->path;
        if (file_exists($f)) {
            unlink($f);
        }
    }
}
