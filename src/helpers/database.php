<?php

require_once __DIR__ . '/../config/database.php';

function dbConnection()
{
    $db = new Database();

    return $db->connect();
}

function dbExecuteOrFail($statement, $message = 'Database operation failed')
{
    if (!$statement->execute()) {
        throw new RuntimeException($message);
    }
}

function dbFetchAll($result)
{
    $rows = [];

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
}
