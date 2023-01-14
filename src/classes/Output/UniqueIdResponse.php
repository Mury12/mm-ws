<?php

namespace MMWS\Output;

use MMWS\Abstracts\Model;

class UniqueIdResponse extends Model
{
    public string $uid;
    public Int $length;
    public string $hash;

    public function __construct($result)
    {
        $this->uid = $result['uid'];
        $this->length = $result['length'];
        $this->hash = $result['hash'];
    }
}
