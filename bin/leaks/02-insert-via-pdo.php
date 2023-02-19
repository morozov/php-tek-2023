<?php

require __DIR__ . '/../dbal.php';
require __DIR__ . '/create-input-stream.php';

$pdo = connect_to_pdo_pgsql()
    ->getNativeConnection();

$attachment = create_input_stream();

printf('Stream created.' . PHP_EOL);
printf('Peak memory usage: %s bytes.' . PHP_EOL . PHP_EOL, number_format(memory_get_peak_usage()));

$stmt = $pdo->prepare('INSERT INTO messages (id, attachment) VALUES (nextval(\'messages_id_seq\'), ?)');
$stmt->bindValue(1, $attachment, PDO::PARAM_LOB);
$stmt->execute();

printf('Row inserted.' . PHP_EOL);
printf('Peak memory usage: %s bytes.' . PHP_EOL, number_format(memory_get_peak_usage()));

fclose($attachment);