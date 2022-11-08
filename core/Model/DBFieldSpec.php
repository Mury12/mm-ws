<?php

namespace MMWS\Model;

use Exception;

class DBFieldSpec
{
    public string $name;
    public string $type;
    public ?bool $nullable;
    public $default;

    const INT = 'int';
    const TINYINT = 'int';
    const DOUBLE = 'float';
    const FLOAT = 'float';
    //TODO: Create a transformer
    const DATETIME = 'string';
    const TIMESTAMP = 'string';
    const DATE = 'string';
    const VARCHAR = 'string';
    const CHAR = 'string';
    // TODO: Needs a transform to JSON on GETTER
    const LONGTEXT = 'string';

    public function __construct(string $name, string $type, ?string $nullable = 'NO', $default = null, ?string $extra)
    {
        $this->name = $name;
        $this->type = self::matchType($type);
        $this->nullable = strtoupper($nullable) === 'YES' || $extra === 'auto_increment' ? true : false;
        $this->default = $default;
        $this->extra = $extra;
    }

    /**
     * Match mysql data type to php type.
     * @param string $typename
     */
    static function matchType(string $typename)
    {
        try {
            return constant('self::' . strtoupper($typename)) ?? '';
        } catch (Exception $e) {
            return '';
        }
    }

    function asParam()
    {
        return ($this->type ? ' ?' : ' ')  . $this->type . " $" . $this->name . ';';
    }

    function asArg()
    {
        $param = $this->type ? '?' : '';
        $param .= $this->type . ' ';
        $param .= '$' . $this->name;
        $param .= ' = null, ';
        return $param;
    }
}
