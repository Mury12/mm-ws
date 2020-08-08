<?php

namespace MMWS\Handler;

use PDO;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Generates controllers, models and entities based on the database name
 * given to this constructor.
 */
class ModelExtractor
{
    /**
     * @var Array $tables transcribed schema
     */
    private $tables = array();
    private $snaked = array();
    /**
     * @var String $dbName the database name
     */
    private $dbName;

    /**
     * @var String $vendor the "VENDOR" name to display in namespace and use class
     */
    private $vendor;

    /**
     * @var String $prefix the name prefix to put on filename PREFIX_ClassName.php
     */
    public $prefix;
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

    function __construct(String $dbName, String $MVCFolderPath, Int $snakeToCamel = 0, String $vendor = 'MMWS', String $prefix = "")
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
        $query .= " AND `TABLE_NAME` NOT LIKE '%view%'";

        $q = $conn->prepare($query);

        if ($r = perform_query_pdo($q)) {
            $r = make_array_from_query($r);

            foreach ($r as $each => $value) {
                
                $className = snake_to_camel($value['TABLE_NAME'], true);
                $this->snaked[$className] = $value['TABLE_NAME'];

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

    function generate()
    {
        $template['model'] = file_get_contents('app/util/templates/Model.template');
        $template['entity'] = file_get_contents('app/util/templates/Entity.template');
        $template['controller'] = file_get_contents('app/util/templates/Controller.template');
        $count = 0;
        foreach ($this->tables as $model => $value) {
            print_r("Generating MVC for " . $model . "...\n");

            $className = $model;
            $m = $this->model($template['model'], $className, $value);
            $e = $this->entity($template['entity'], $className);
            $c = $this->controller($template['controller'], $className);

            $file['model'] = fopen($this->MVCFolderPath . '/models/' . $this->prefix . $className . '.php', 'w');
            $file['entity'] = fopen($this->MVCFolderPath . '/entities/' . $this->prefix . $className . 'Entity.php', 'w');
            $file['controller'] = fopen($this->MVCFolderPath . '/controllers/' . $this->prefix . $className . 'Controller.php', 'w');

            try {
                fwrite($file['model'], $m);
                fwrite($file['entity'], $e);
                fwrite($file['controller'], $c);
                $count += 3;
            } catch (FileException $f) {
                throw $f;
            }
        }
        print_r("Done!\n");
        print_r("Total files: " . $count);
    }

    private function model($template, String $model, array $values)
    {
        $attributes = "";
        $constructor = "";
        $attrSetter = "";
        foreach ($values as $value) {
            $attributes .= "public $" . $value . ";\n    ";
            $constructor .= "$" . $value . " = null, ";
            $attrSetter .= '$this->' . $value . " = $" . $value . ";\n\t    ";
        }
        $constructor = trim($constructor, ', ');
        $output = str_replace('{CLASS_ATTRIBUTES}', $attributes, $template);
        $output = str_replace('{CLASS_NAME}', $model, $output);
        $output = str_replace('{TABLE_NAME}', "'".$this->snaked[$model] . "';\n", $output);
        $output = str_replace('{CONSTRUCTOR}', $constructor, $output);
        $output = str_replace('{ENTITY}', $model . "Entity", $output);
        $output = str_replace('{VENDOR}', $this->vendor, $output);
        $output = str_replace('{ATTRIBUTE_SET}', $attrSetter, $output);
        return $output;
    }

    private function entity($template, String $model)
    {
        $output = str_replace('{CLASS_NAME}', $model . 'Entity', $template);
        $output = str_replace('{USE_CLASS}', $model, $output);
        $output = str_replace('{VENDOR}', $this->vendor, $output);
        return $output;
    }

    private function controller($template, String $model)
    {
        $output = str_replace('{CLASS_NAME}', $model . 'Controller', $template);
        $output = str_replace('{USE_CLASS}', $model, $output);
        $output = str_replace('{VENDOR}', $this->vendor, $output);
        return $output;
    }

    function getTables()
    {
        return $this->tables;
    }
}
