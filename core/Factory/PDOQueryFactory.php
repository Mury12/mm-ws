<?php

namespace MMWS\Factory;

use MMWS\Interfaces\Factory;
use PDOStatement;
use ValueError;

class PDOQueryFactory implements Factory
{
    /**
     * @param array $stmt array with query params. 
     * If "query" index is not set, then it will throw an error.
     * 
     * @param array $stmt query statement
     * 
     * ```php
     * $pdo = PDOQueryFactory::create(['query' => 'SELECT id FROM user']);
     * if($pdo->execute()){
     *      // ...
     * }
     * 
     * ```
     * @return PDOStatement 
     */
    static function create(array $stmt): PDOStatement
    {
        if (!array_key_exists('query', $stmt))
            throw new ValueError('Statement array must be bound with a query index and it was not found.', 500);
        global $conn;

        return $conn->prepare($stmt['query']);
    }

    /**
     * Creates a query instance with a transaction returning
     * PDO Statement and assigning $trx by reference.
     * 
     * @param array $stmt query statement
     * @param $trx will be set to a PDO object
     * ```php
     * // PDO object to use transactions
     * $trx;
     * $pdo = PDOQueryFactory::transaction(['query' => 'SELECT id FROM user'], $trx);
     * if($pdo->execute()){
     *      // ...
     *      $trx->commit();
     * }else{
     *      $trx->rollback();
     * }
     * 
     * ```
     * @return PDOStatement
     */
    static function transaction(array $stmt, &$trx): PDOStatement
    {
        if (!array_key_exists('query', $stmt))
            throw new ValueError('Statement array must be bound with a query index and it was not found.', 500);
        global $conn;

        $trx = $conn;
        if (!$trx->inTransaction()) {
            $trx->beginTransaction();
        }
        return $trx->prepare($stmt['query']);
    }
}
