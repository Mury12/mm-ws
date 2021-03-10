<?php

namespace MMWS\Handler;

use MMWS\Factory\PDOQueryFactory;
use MMWS\Factory\RequestExceptionFactory;
use PDOException;
use TypeError;
use ValueError;

/**
 * Handle Database query operations
 * 
 * -------------
 * 
 * Example Usage:
 * 
 * ```php
 * $stmt = new PDOQueryBuilder('my_table');
 * $stmt->select(['id as userId', 'name']);
 * $stmt->and('name', 'john');
 * $result = $stmt->run();
 * ```
 * -------------
 * @package MMWS
 * @author Andre Mury <mury_gh@hotmail.com>
 * @version MMWS^0.9.6-alpha
 */
class PDOQueryBuilder
{
    protected $filters = [];
    protected $and = "";
    protected $or = "";
    protected $offset = 0;
    protected $page = 0;
    protected $limit = 3;
    protected $stmt = null;
    protected $query = "";
    private $queryType = null;
    private $queryEnd = "";
    protected $orderBy = false;
    private $data = [];
    private $table = "";
    private $hasWhere = false;

    const QUERY_SELECT = 1;
    const QUERY_INSERT = 2;
    const QUERY_UPDATE = 3;
    const QUERY_DELETE = 4;

    function __construct(string $tableName, int $limit = 3, int $page = 0)
    {
        $this->table = $tableName;
        $this->limit = $limit;
        $this->page = $page;

        $this->offset = ($page * $limit);
    }

    /**
     * Runs the built query and returns its result fetched in the chosen fetch style.
     * @see https://www.php.net/manual/pt_BR/book.pdo.php for more information about
     * PDO fetch styles.
     * @param int $fetchStyle PDO Fetch style. 
     * 
     * @return array
     */
    function run(int $fetchStyle = \PDO::FETCH_NAMED): array
    {
        try {
            $this->check();
            $this->query = str_replace("  ", " ", $this->query);
            $stmt = PDOQueryFactory::create(['query' => $this->query]);
            $this->stmt = perform_query_pdo($stmt);
            if ($this->queryType === self::QUERY_SELECT) {
                return $this->stmt->fetchAll($fetchStyle);
            } else return [];
        } catch (PDOException $e) {
            throw $e;
        }
    }

    private function check()
    {
        if ($this->queryType === self::QUERY_SELECT) {
            if ($this->orderBy) {
                $this->query .= " " . $this->orderBy;
            }
            $this->query .= " LIMIT " . $this->limit . " OFFSET " . $this->offset;
        } elseif ($this->queryType === self::QUERY_DELETE || $this->queryType === self::QUERY_UPDATE) {
            if (!$this->hasWhere)
                throw RequestExceptionFactory::create("Trying to execute DELETE from table without WHERE statement.", 403);
        } elseif ($this->queryType === 0) {
            throw new TypeError("Trying to execute query without a query type set", 500);
        }
    }

    /**
     * Insert values into the given table.
     * @param array $fields the indexed field=>values to insert.
     * 
     * ```php
     * $stmt->insert(['name'=>'john', 'gender'=>'male']);
     * $stmt->run();
     * ```
     */
    function insert(array $fields)
    {
        $this->queryType = self::QUERY_INSERT;
        if (sizeof($fields)) {
            $columns = [];
            $values = [];

            foreach ($fields as $field => $value) {
                $columns[] =  $this->sanitize(preg_replace('/[^a-zA-Z0-9\-_]+/igm', '', $field));
                $values[] = $this->sanitize($value);
            }
            $this->query = "INSERT INTO " . $this->table . " () VALUES ()";
            return $this;
        } else {
            throw new ValueError("Array fields must not be empty", 400);
        }
    }

    /**
     * Creates a SELECT statement
     * @param array $fields the fields to be selected. If none is set, then will select *. Aliases can be used.
     * 
     * ```php
     * $stmt->select(['id as userId', 'name', 'email']);
     * ```
     * 
     * @return PDOQueryBuilder
     */
    function select(array $fields = ['*']): PDOQueryBuilder
    {
        $this->queryType = self::QUERY_SELECT;

        $columns = array_map(function ($item) {
            return $this->sanitize($item);
        }, $fields);
        $this->query = "SELECT " . implode(', ', $columns);
        $this->query .= " FROM " . $this->table;
        return $this;
    }

    /**
     * Creates a DELETE statement
     * @param array $fields the fields to be selected. If none is set, then will select *. Aliases can be used.
     * 
     * ```php
     * $stmt->select(['id as userId', 'name', 'email']);
     * ```
     * 
     * @return PDOQueryBuilder
     */
    function delete(): PDOQueryBuilder
    {
        $this->queryType = self::QUERY_DELETE;

        $this->query = "DELETE FROM " . $this->table;
        return $this;
    }

    /**
     * Creates an UPDAYE statement
     * @param array $fields the fields to be selected. If none is set, then will select *. Aliases can be used.
     * 
     * ```php
     * $stmt->update(['name'=> 'John', 'email'=>'john@mail.com']);
     * ```
     * 
     * @return PDOQueryBuilder
     */
    function update(array $fields): PDOQueryBuilder
    {
        $this->queryType = self::QUERY_UPDATE;

        $this->query = "UPDATE " . $this->table;
        $this->query .= " SET ";
        $update = "";
        foreach ($fields as $field => $val) {
            $value = $this->sanitize($val);
            $column = $this->sanitize($field);
            $update .= " `$column` = '$value', ";
        }

        $update = trim($update, ', ');

        $this->query .= " $update";
        return $this;
    }

    /**
     * Adds the ORDER BY directive into the select.
     * 
     * @param array $fields array of fields as index and order direction as value
     * 
     * ```php
     * $stmt->order(['id' => 'ASC', 'name' => 'DESC']);
     * ```
     * 
     * @return PDOQueryBuilder
     */
    function order(array $fields): PDOQueryBuilder
    {
        $orderBy = ' ORDER BY';

        foreach ($fields as $field => $val) {
            $value = $this->sanitize($val);
            $orderBy .= " `$field` $value, ";
        }
        $orderBy = trim($orderBy, ", ");
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * Adds an AND operator to the string.
     * @param string $field the column name 
     * @param string $val the value to be matched
     * @param string $op optional operator.
     * Supports SQL operators (<,>,<=,>=,<>,=,!=,LIKE,NOT LIKE) to simulate IS NULL, use value = NULL and operador = IS;
     * 
     * ```php
     * $stmt->and('name', 'John', 'LIKE');
     * ```
     * @return PDOQueryBuilder
     */
    function and(string $field, string $val, string $op = '='): PDOQueryBuilder
    {
        $value = $this->sanitize($val);
        $braces = $op == 'LIKE' ? '%' : '';
        if (!$this->hasWhere) {
            $this->query .= " WHERE `$field` $op '" . $braces . $value . $braces . "'";
            $this->hasWhere = true;
        } else
            $this->query .= " AND `$field` $op '" . $braces . $value . $braces . "'";
        return $this;
    }

    /**
     * Adds an OR operator to the string.
     * @param string $field the column name 
     * @param string $val the value to be matched
     * @param string $op optional operator.
     * Supports SQL operators (<,>,<=,>=,<>,=,!=,LIKE,NOT LIKE) to simulate IS NULL, use value = NULL and operador = IS;
     * 
     * ```php
     * $stmt->or('name', 'John', 'LIKE');
     * ```
     * @return PDOQueryBuilder
     */
    function or(string $field, string $val, string $op = 'LIKE'): PDOQueryBuilder
    {
        $value = $this->sanitize($val);
        $braces = $op == 'LIKE' ? '%' : '';

        $or = "`$field` $op '" . $braces . $value . implode("$braces' OR `$field` LIKE '" . $braces . $value . "'") . "$braces'";

        if (!$this->hasWhere) {
            $this->query .= " WHERE ($or)";
            $this->hasWhere = true;
        } else
            $this->query = " ($or)";
        return $this;
    }

    /**
     * Set an indexed filter array as an OR or AND operation to the string
     * 
     * @param array $filters 
     * @param bool $and if true will use AND otherwise will use OR
     * 
     * ```
     * $stmt->([])
     * ```
     */
    function setFilters(array $filters, bool $and = false): PDOQueryBuilder
    {
        $aggregator = $and ? 'AND' : 'OR';
        $and = [];
        foreach ($filters as $filter => $val) {
            $value = $this->sanitize($val);
            if (stripos($value, '|')) {
                $values = explode('|', $value);
                $str = " (`$filter` LIKE '%" . implode("%' $aggregator `$filter` LIKE '%", $values) . "%')";
            } else $str = "`$filter` LIKE '%$value%'";
            $and[] = $str;
        }
        $this->query .= " WHERE " . implode(" $aggregator ", $and);
        return $this;
    }

    function maxRows(int $limit): PDOQueryBuilder
    {
        $this->limit = $limit;
        return $this;
    }

    function setPage(int $page): PDOQueryBuilder
    {
        $this->page = $page;
        $this->offset = $this->limit * $this->page;
        return $this;
    }

    function sanitize($val): string
    {
        return addslashes(filter_var($val, FILTER_SANITIZE_STRIPPED));
    }
}
