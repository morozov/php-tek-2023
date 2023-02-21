<?php

declare(strict_types=1);

require __DIR__ . '/../dbal.php';
require __DIR__ . '/blob-functions.php';

$pdo = connect_to_pdo_pgsql()
    ->getNativeConnection();

$attachment = create_input_stream();

printf('Stream created.' . PHP_EOL);
print_peak_memory_usage();

$stmt = $pdo->prepare('INSERT INTO messages (id, attachment) VALUES (nextval(\'messages_id_seq\'), ?)');
$stmt->bindValue(1, $attachment, PDO::PARAM_LOB);
$stmt->execute();

printf('Row inserted.' . PHP_EOL);
print_peak_memory_usage();

fclose($attachment);
