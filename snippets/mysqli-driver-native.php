<?php

declare(strict_types=1);

function getMessageSubjects(mysqli $conn, int $userId): array
{
    $query = <<<'SQL'
    SELECT subject FROM messages WHERE user_id=?
    SQL;

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();

    $stmt->bind_result($subject);

    $subjects = [];
    while ($stmt->fetch() === true) {
        $subjects[] = $subject;
    }

    return $subjects;
}

$conn = mysqli_connect('127.0.0.1', 'root', '', 'php-tek-2023');

if (! $conn) {
    exit(1);
}

$subjects = getMessageSubjects($conn, 1);
var_dump($subjects);
