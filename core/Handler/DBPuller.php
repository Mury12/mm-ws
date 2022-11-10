<?php

namespace MMWS\Handler;

use Dotenv\Exception\InvalidFileException;
use MMWS\Handler\CaseHandler;
use MMWS\Model\DBFieldSpec;
use MMWS\Model\DBTableSpec;
use PDO;
use PDOStatement;


/**
 * Generates controllers, models and entities based on the database name
 * given to this constructor.
 * 
 * @param String $dbName database name 
 * @param String $MVCFolderPath project folder containing the models, controllers and entities folders
 * @param Int $snakeToCamel convert column names from snake_case to camelCase
 * set 1 to camelCase, 2 to CamelCase and default 0 is none. Table names will
 * be automatically converted to CamelCase
 * @param String $vendor the "VENDOR" name to display in namespace and use class
 * @param String $prefix the name prefix to put on filename PREFIX_ClassName.php
 * 
 * -------------
 * 
 * Example Usage:
 * 
 * use MMWS\Handler\DatabaseModelExtractor;
 * 
 * $dbm = new DatabaseModelExtractor('mm_dbname', 'src/partials/classes', 1);
 * 
 * $dbm->generate();
 * 
 * -------------
 * @package MMWS
 * @author Andre Mury <mury_gh@hotmail.com>
 * @version MMWS^0.9.1-alpha
 */
class DBPuller
{
    /**
     * @var DBTableSpec[] $tables transcribed schema
     */
    private $tables = [];

    /**
     * @var Array<String> $snaked raw table names
     */
    private $snaked = [];

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
     * @var string[] $tablesIncluded if set, will only migrate this tables
     */
    private $tablesIncluded = array();

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
        $this->prefix = $prefix;
    }

    /**
     * Set tables to extract. If this method is called, it will only
     * extract the set tables.
     * 
     * @return DatabaseModelExtract the instance itself
     */
    function setTables(array $tables): DBPuller
    {
        $this->tablesIncluded = $tables;
        return $this;
    }

    /**
     * Get all the remote tables located on the given database
     * 
     * @return Bool
     */
    private function getRemoteTables()
    {
        global $conn;

        $query  = " SELECT `TABLE_NAME`, `COLUMN_NAME`, `COLUMN_DEFAULT`, `IS_NULLABLE`, `DATA_TYPE`, `EXTRA`";
        $query .= " FROM `INFORMATION_SCHEMA`.`COLUMNS` ";
        $query .= " WHERE `TABLE_SCHEMA` = '" . $this->dbName . "'";
        $query .= " AND `TABLE_NAME` NOT LIKE '%view%'";

        if (sizeof($this->tablesIncluded)) {
            $query .= " AND TABLE_NAME in ('" . implode("','", $this->tablesIncluded) . "')";
        }

        $q = $conn->prepare($query);

        if ($r = $this->perform_query_pdo($q)) {
            $r = $this->make_array_from_query($r);

            foreach ($r as $each => $value) {

                $className = CaseHandler::convert($value['TABLE_NAME'], 0, true);
                $this->snaked[$className] = $value['TABLE_NAME'];

                if (!array_key_exists($className, $this->tables)) {
                    $table = new DBTableSpec($className);
                    $this->tables[$className] = $table;
                } else {
                    $table = $this->tables[$className];
                }

                $columnName = $this->snakeToCamel > 0
                    ? CaseHandler::convert(
                        $value['COLUMN_NAME'],
                        0,
                        $this->snakeToCamel === 2 ? true : false
                    ) : $value['COLUMN_NAME'];

                $table->addField(new DBFieldSpec(
                    $columnName,
                    $value['DATA_TYPE'],
                    $value['IS_NULLABLE'],
                    $value['COLUMN_DEFAULT'],
                    $value['EXTRA']
                ));
            }

            return true;
        }
        return false;
    }

    /**
     * Generates the models based on the tables
     * returned in the main query using the given
     * db_name.
     * 
     * @return void
     */
    function generate()
    {
        $this->getRemoteTables();
        $template['model'] = file_get_contents('src/util/templates/classes/Model.template');
        $template['entity'] = file_get_contents('src/util/templates/classes/Entity.template');
        $template['controller'] = file_get_contents('src/util/templates/classes/Controller.template');
        $count = 0;
        foreach ($this->tables as $model => $value) {
            print_r("Generating MVCE for " . $model . "...\n");

            $className = $model;
            $m = $this->model(
                $template['model'],
                $className,
                $value
            );
            $e = $this->entity($template['entity'], $className);
            $c = $this->controller($template['controller'], $className);

            $file['model'] = fopen($this->MVCFolderPath . '/Model/' . $this->prefix . $className . '.php', 'w');
            $file['entity'] = fopen($this->MVCFolderPath . '/Entity/' . $this->prefix . $className . 'Entity.php', 'w');
            $file['controller'] = fopen($this->MVCFolderPath . '/Controller/' . $this->prefix . $className . 'Controller.php', 'w');

            try {
                fwrite($file['model'], $m);
                fwrite($file['entity'], $e);
                fwrite($file['controller'], $c);
                $count += 3;
            } catch (InvalidFileException $f) {
                throw $f;
            }
        }
        print_r("Done!\n");
        print_r("Total files: " . $count . "\n");
    }

    /**
     * Generate the models based in the templates
     * 
     * @param String $template the template directory
     * @param String $model the class name
     * @param Array $values array of column names
     * 
     * @return String the fulfilled template content
     */
    private function model($template, String $model, DBTableSpec $table)
    {
        $attributes = "";
        $constructor = "";
        $attrSetter = "";
        foreach ($table->getFields() as $field) {
            $attributes .= "protected" . $field->asParam() . "\n    ";
            $constructor .= $field->asArg();
            $attrSetter .= '$this->' . $field->name . " = $" . $field->name . ";\n\t    ";
        }
        $constructor = trim($constructor, ', ');
        $output = str_replace('{CLASS_ATTRIBUTES}', $attributes, $template);
        $output = str_replace('{CLASS_NAME}', $model, $output);
        $output = str_replace('{CONSTRUCTOR}', $constructor, $output);
        $output = str_replace('{VENDOR}', $this->vendor, $output);
        $output = str_replace('{ATTRIBUTE_SET}', $attrSetter, $output);
        return $output;
    }

    /**
     * Generate the entities based in the templates
     * 
     * @param String $template the template directory
     * @param String $model the class name
     * 
     * @return String the fulfilled template content
     */
    private function entity($template, String $model)
    {
        $output = str_replace('{CLASS_NAME}', $model . 'Entity', $template);
        $output = str_replace('{TABLE_NAME}', "'" . $this->snaked[$model] . "';\n", $output);
        $output = str_replace('{USE_CLASS}', $model, $output);
        $output = str_replace('{VENDOR}', $this->vendor, $output);
        return $output;
    }

    /**
     * Generate the controllers based in the templat
     * 
     * @param String $template the template directory
     * @param String $model the class names
     * 
     * @return String the fulfilled template content
     */
    private function controller($template, String $model)
    {
        $output = str_replace('{CLASS_NAME}', $model . 'Controller', $template);
        $output = str_replace('{USE_CLASS}', $model, $output);
        $output = str_replace('{VENDOR}', $this->vendor, $output);
        return $output;
    }

    /**
     * Return raw table names from the database
     * @return Array<String>
     */
    function getTables()
    {
        return $this->snaked;
    }

    /**
     * Return indexed table names after conversion
     * 
     * @return Array<Array<String>>
     */
    function getConvertedTables()
    {
        return $this->tables;
    }

    /**
     * Performs a try catch default request with PDO
     * @param  PDOStatement $request the PDO prepared statement
     * @return PDOStatement|Bool unfetched result or false
     */
    private function perform_query_pdo(PDOStatement $request, Bool $show_errors = false)
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
    private function make_array_from_query(PDOStatement $q, String $cls = null, Bool $map = false)
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
}
