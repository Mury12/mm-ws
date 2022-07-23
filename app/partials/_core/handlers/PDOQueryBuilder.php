<?php

namespace MMWS\Handler;

use MMWS\Factory\PDOQueryFactory;
use MMWS\Factory\RequestExceptionFactory;
use PDOException;
use PDOStatement;
use TypeError;
use ValueError;

/**
 * Handle Database query operations.
 *
 * Presents methods to execute queries in mysql database
 * escaping and filtering variables.
 * 
 * Note that the UPDATE and DELETE statements MUST BE followed by a `WHERE` parameter.
 * 
 * To add a `WHERE` parameter, call `$stmt->where($field, $value)` function.
 * 
 * -------------
 * 
 * Example Usage:
 * 
 * ```php
 * $stmt = new PDOQueryBuilder('my_table');
 * 
 * // Insert John
 * $stmt->insert(['name' => 'jon']);
 * $result = $stmt->run();
 * 
 * // Select John
 * $stmt->select(['id as userId', 'name']);
 * $stmt->where('name', 'jon');
 * $result = $stmt->run();
 * 
 * $stmt->update(['email' => 'john@mail.com', 'name' => 'john'])
 * $stmt->where('id', 1);
 * $result = $stmt->run();
 * 
 * // Delete Jogn
 * $stmt->delete();
 * $stmt->where('id', 1);
 * $result = $stmt->run();
 * 
 * // Raw query
 * $result = $stmt->raw('SELECT * FROM my_table WHERE id = ? OR name = ?', [1, 'john']);
 * 
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
    protected $orderBy = false;
    private $table = "";
    private $hasWhere = false;
    /**
     * @var \PDO $trx
     */
    private $trx;
    /**
     * @var bool $autoCommit if should or not auto commit queries.
     * 
     */
    private $autoCommit = true;

    const QUERY_SELECT = 1;
    const QUERY_INSERT = 2;
    const QUERY_UPDATE = 3;
    const QUERY_DELETE = 4;
    const QUERY_RAW = 5;

    function __construct(string $tableName, $limit = 3, $page = 1)
    {
        $this->table = $tableName;
        $this->limit = $limit >= 1 ? $limit : 1;
        $this->page = $page >= 1 ? $page - 1 : 0;
        $offset = (($this->page) * $this->limit);
        $this->offset = $offset >= 0 ? $offset : 0;
    }

    /**
     * Runs the built query and returns its result fetched in the chosen fetch style if elegible.
     * @see https://www.php.net/manual/pt_BR/book.pdo.php for more information about
     * PDO fetch styles.
     * 
     * > If the query type RAW is selected an error will be thrown.
     * > To run a different query after a RAW query, use the 
     * > action selectors such as select, insert, update or delete.
     * 
     * If query type INSERT is chosen, this will return an array with the last inserted id
     * as 
     * ```php
     * $stmt->insert(['username', 'john']);
     * $result = $stmt->run();
     * print_r($result);
     * /// result 
     * [
     *      'id' => 1
     * ]
     * ```
     * -----
     * Example 
     * ```php
     * $stmt->select(['username', 'email']);
     * $result = $stmt->run();
     * ```
     * 
     * @param int $fetchStyle PDO Fetch style. 
     * @return array
     */
    function run(int $fetchStyle = \PDO::FETCH_NAMED): array
    {
        try {
            if ($this->queryType !== self::QUERY_RAW) {
                $this->check();
                $this->query = preg_replace("/[ ]+/im", " ", $this->query);

                $stmt = PDOQueryFactory::transaction(['query' => $this->query], $this->trx);

                $result = [];
                $this->stmt = perform_query_pdo($stmt, DEBUG_MODE);
                if ($this->stmt) {
                    if ($this->queryType === self::QUERY_SELECT) {
                        if ($this->autoCommit) $this->commit();
                        $result = $this->stmt->fetchAll($fetchStyle);
                    } else if ($this->queryType === self::QUERY_INSERT) {
                        $id  = $this->trx->lastInsertId();
                        if ($this->autoCommit) $this->commit();
                        $result = ['id' => $id];
                    } elseif ($this->autoCommit) {
                        $this->commit();
                    }
                    $this->restart();
                    return $result;
                }
                $this->restart();
            } else {
                $this->rollBack();
                throw new TypeError("Trying to run an undefined RAW QUERY. Please choose another type of query to perform a run.");
            }
        } catch (PDOException $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Enables or disables the auto commit function.
     * If disabled, the queries performed at `run()` will not be
     * commited and may stuck the database.
     * 
     * @param bool $enable
     * 
     * ```php
     * $stmt = new PDOQueryBuilder('my_table');
     * $stmt->shouldCommit(false);
     * try {
     *      $stmt->insert(['username'=>'john']);
     *      $stmt->run();
     *      
     *      $stmt->insert(['username'=>'james']);
     *      $stmt->run();
     *      
     *      $stmt->insert(['username'=>'Kevin']);
     *      $stmt->run();
     *      
     *      $stmt->commit();
     * } catch(PDOException $e) {
     *      $stmt->rollback();
     *      throw $e;
     * }
     * ```
     * 
     */
    public function shouldCommit(bool $enable = true): PDOQueryBuilder
    {
        $this->autoCommit = $enable;
        return $this;
    }

    /**
     * Commits a transaction
     * 
     * @return PDOQueryBuilder
     */
    public function commit(): PDOQueryBuilder
    {
        if ($this->trx && $this->trx instanceof \PDO) {
            try {
                $this->trx->commit();
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return $this;
    }

    /**
     * Rolls back a transaction
     * 
     * @return PDOQueryBuilder
     */
    public function rollback(): PDOQueryBuilder
    {
        if ($this->trx && $this->trx instanceof \PDO) {
            try {
                $this->trx->rollback();
            } catch (PDOException $e) {
                throw $e;
            }
        }
        return $this;
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
                $columns[] =  $this->sanitize(preg_replace('/[^a-zA-Z0-9\-_]+/im', '', $field));
                $values[] = $this->sanitize($value);
            }
            $this->query = "INSERT INTO " . $this->table . " (`" . implode('`, `', $columns) . "`) VALUES ('" . implode("', '", $values) . "')";
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
     * Executes a RAW query and returns it unfetched as PDOStatement object.
     * 
     * @param string $rawQuery the actual query
     * @param array $bindParams array of params to bind.
     * 
     * ```php
     * $stmt->raw('INSERT INTO users (username, email) VALUES (?, ?)', ['john', 'john@mail.com']);
     * ```
     * @return PDOStatement
     */
    function raw(string $rawQuery, array $bindParams = null): PDOStatement
    {
        $stmt = PDOQueryFactory::create(['query' => $rawQuery]);
        if ($bindParams) {
            $i = 0;
            for ($i; $i < sizeof($bindParams); $i++) {
                $index = $i + 1;
                $stmt->bindParam($index, $bindParams[$i]);
            }
        }
        $result = perform_query_pdo($stmt, DEBUG_MODE);
        if ($result) {
            return $result;
        }
        throw RequestExceptionFactory::create("Houve um erro ao processar esta requisição.", 500);
    }

    /**
     * Creates a DELETE statement
     * To run a delete statement, a WHERE Must be placed in order to avoid
     * catastrophic mass deletions accidentaly.
     * 
     * ```php
     * $stmt->delete();
     * $stmt->and('id', 2);
     * $stmt->run();
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
     * To eprform an update statement, a WHERE must be placed in order to avoid
     * accidental unwanted updates.
     * 
     * @param array $fields the fields to be updated with its values, as below
     * 
     * ```php
     * $stmt->update(['name'=> 'John', 'email'=>'john@mail.com']);
     * $stmt->and('id', 1);
     * $stmt->run();
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
     * If it is the first call, a WHERE clause will be added, combined to the `PDOQueryBuilder::or` method.
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
            $val === 'NULL'
                ? $this->query .= " OR `$field` $op NULL"
                : $this->query .= " AND `$field` $op '" . $braces . $value . $braces . "'";
        return $this;
    }

    /**
     * Adds a WHERE operator to the string.
     * If it is the first call, a WHERE clause will be added, combined to the `PDOQueryBuilder::or` method.
     * If it's not, an AND operator will be added.
     * 
     * Alias to the `and` method.
     * 
     * @param string $field the column name 
     * @param string $val the value to be matched
     * @param string $op optional operator.
     * Supports SQL operators (<,>,<=,>=,<>,=,!=,LIKE,NOT LIKE) to simulate IS NULL, use value = NULL and operador = IS;
     * 
     * ```php
     * $stmt->where('name', 'John', 'LIKE');
     * ```
     * @return PDOQueryBuilder
     */
    function where(string $column, string $val, string $op = '=')
    {
        return $this->and($column, $val, $op);
    }

    /**
     * Adds an OR operator to the string.
     * If it is the first call, a WHERE clause will be added, combined to the `PDOQueryBuilder::and` method.
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
            $val === 'NULL'
                ? $this->query .= " OR `$field` $op NULL"
                : $this->query .= " AND `$field` $op '" . $braces . $value . $braces . "'";

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
    function setFilters(array $filters, bool $and = true): PDOQueryBuilder
    {
        $aggregator = $and ? 'AND' : 'OR';
        $and = [];
        foreach ($filters as $filter => $val) {
            $value = $this->sanitize($val);
            if (stripos($value, '|')) {
                $values = explode('|', $value);
                $str = " (`$filter` LIKE '%" . implode("%' OR `$filter` LIKE '%", $values) . "%')";
            } else $str = "`$filter` LIKE '%$value%'";
            $and[] = $str;
        }
        $this->query .= ($this->hasWhere ? ' AND ' : ' WHERE ') . '(' . implode(" $aggregator ", $and) . ')';
        if (!$this->hasWhere)
            $this->hasWhere = true;
        return $this;
    }
    
    function search(array $fields, string $query)
    {
        try {
            $values = [];
            preg_match_all('/([a-zA-Z0-9]+)/im', urldecode($query), $values);
            if (sizeof($values)) {
                $queryStr = [];
                foreach ($fields as $field) {
                    $fullStr = urldecode($query);
                    $queryStr[] = "`$field` LIKE '%$fullStr%'";
                    foreach ($values[0] as $value) {
                        if (strtolower(mb_substr($value, -1)) === 's') {
                            $substr = substr_replace($value, '', -1);
                            $queryStr[] = "`$field` LIKE '%$substr%'";
                        }
                        $queryStr[] = "`$field` LIKE '%$value%'";
                    }
                }
            }
            $this->query .= ($this->hasWhere ? " AND " : "OR") . "(" . implode(" OR ", $queryStr) . ")";
            return $this;
        } catch (\PDOException $th) {
            throw $th;
        }
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

    function restart()
    {
        $this->filters = [];
        $this->and = "";
        $this->or = "";
        $this->offset = 0;
        $this->stmt = null;
        $this->query = "";
        $this->queryType = null;
        $this->queryEnd = "";
        $this->orderBy = false;
        $this->hasWhere = false;
    }
}
