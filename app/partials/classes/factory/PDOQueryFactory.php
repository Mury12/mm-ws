<?php

namespace MMWS\Factory;

use MMWS\Interfaces\IFactory;
use PDOStatement;
use ValueError;

class PDOQueryFactory implements IFactory
{
    /**
     * @param array $stmt array with query params. 
     * If "query" index is not set, then it will throw an error.
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
}
