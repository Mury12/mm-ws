<?php

namespace MMWS\Handler;

use PDO;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ModelExtractor
{
    /**
     * @var Array $tables transcribed schema
     */
    private $tables = array();

    /**
     * @var String $dbName the database name
     */
    private $dbName;

    /**
     * @var String $vendor the "VENDOR" name to display in namespace and use class
     */
    private $vendor;

    /**
     * @var String $MVCFolderPath the path to save the models. 
     * Must contain the folders models/ entities/ and controllers/.
     */
    private $MVCFolderPath;
    /**
     * @var Int $snakeToCamel convert column names from snake_case to camelCase
     * set 1 to camelCase, 2 to CamelCase and default 0 is none. Table names will
     * be automatically converted to CamelCase
     */
    private $snakeToCamel;

    function __construct(String $dbName, String $MVCFolderPath, $snakeToCamel = 0, $vendor = 'MMWS')
    {
        $this->dbName = $dbName;
        $this->MVCFolderPath = $MVCFolderPath;
        $this->snakeToCamel = $snakeToCamel;
        $this->vendor = $vendor;

        $this->getRemoteTables();
    }

    /**
     * Get all the remote tables located on the given database
     * @return Bool
     */
    private function getRemoteTables()
    {
        global $conn;

        $query  = " SELECT `TABLE_NAME`, `COLUMN_NAME`";
        $query .= " FROM `INFORMATION_SCHEMA`.`COLUMNS` ";
        $query .= " WHERE `TABLE_SCHEMA` = '" . $this->dbName . "'";

        $q = $conn->prepare($query);

        if ($r = perform_query_pdo($q)) {
            $r = make_array_from_query($r);

            foreach ($r as $each => $value) {
                $className = snake_to_camel($value['TABLE_NAME'], true);

                $this->tables[$className][] = $this->snakeToCamel > 0
                    ? snake_to_camel(
                        $value['COLUMN_NAME'],
                        $this->snakeToCamel === 2 ? true : false
                    ) : $value['COLUMN_NAME'];
            }

            return true;
        }
        return false;
    }

    function generateModels()
    {
        $template = file_get_contents('app/util/templates/Model.template');

        foreach ($this->tables as $model => $value) {
            $className = $model;
            $output = $this->replaceAttributes($template, 'DcUsers', $value);
        }
        $file = fopen($this->MVCFolderPath . '/models/' . $className . '.php', 'w');
        try {
            fwrite($file, $output);
        } catch (FileException $f) {
            throw $f;
        }
    }

    private function replaceAttributes($template, String $model, array $values)
    {
        $attributes = "";
        $constructor = "";
        $attrSetter = "";
        foreach ($values as $value) {
            $attributes .= "public $" . $value . ";\n    ";
            $constructor .= "$" . $value . " = null, ";
            $attrSetter .= '$this->' . $value . " = $" . $value . ";\n\t";
        }
        $constructor = trim($constructor, ', ');
        $output = str_replace('{CLASS_ATTRIBUTES}', $attributes, $template);
        $output = str_replace('{CLASS_NAME}', $model, $output);
        $output = str_replace('{TABLE_NAME}', $model . ";\n", $output);
        $output = str_replace('{CONSTRUCTOR}', $constructor, $output);
        $output = str_replace('{ENTITY}', $model . "Entity", $output);
        $output = str_replace('{USE_CLASS}', $model . "Entity", $output);
        $output = str_replace('{VENDOR}', $this->vendor, $output);
        $output = str_replace('{ATTRIBUTE_SET}', $attrSetter, $output);
        return $output;
    }

    function getTables()
    {
        return $this->tables;
    }
}
