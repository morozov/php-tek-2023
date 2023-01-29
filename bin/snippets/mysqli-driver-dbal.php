<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

function getMessageSubjects(Connection $conn, int $userId): array {
    $query = <<<'SQL'
    SELECT subject FROM messages WHERE user_id=?
    SQL;

    return $conn->fetchFirstColumn($query, [$userId]);
}

require __DIR__ . '/../vendor/autoload.php';

$conn = DriverManager::getConnection([
    'driver' => 'mysqli',
    'host' => '127.0.0.1',
    'user' => 'root',
    'dbname' => 'php-tek-2023',
]);

$subjects = getMessageSubjects($conn, 1);
var_dump($subjects);
