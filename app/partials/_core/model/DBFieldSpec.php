<?php

namespace MMWS\Model;

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
    const DATETIME = '\DateTime';
    const TIMESTAMP = '\DateTime';
    const DATE = '\DateTime';
    const VARCHAR = 'string';
    const CHAR = 'string';

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
        return constant('self::' . strtoupper($typename)) ?? '';
    }

    function asParam()
    {
        return '?'  . $this->type . " $" . $this->name . ';';
    }

    function asArg()
    {
        $param = '?';
        $param .= $this->type . ' ';
        $param .= '$' . $this->name;
        $param .= ' = null, ';
        return $param;
    }
}
