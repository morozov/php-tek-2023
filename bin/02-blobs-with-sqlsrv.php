<?php

declare(strict_types=1);

use Morozov\PhpTek2023\ConnectionProvider;

require __DIR__ . '/../vendor/autoload.php';

$conn = (new ConnectionProvider())
    ->getConnection('sqlsrv')
    ->getNativeConnection();

$stmt = sqlsrv_query($conn, 'SELECT attachment FROM messages WHERE id = 1');

if (sqlsrv_fetch($stmt) === false) {
    die('Could not fetch row.');
}

$attachment = sqlsrv_get_field($stmt, 0, sqlsrv_phptype_stream(SQLSRV_ENC_BINARY));

$output = fopen('/dev/null', 'w');
$copied = stream_copy_to_stream($attachment, $output);

printf('Copied %d.' . PHP_EOL, $copied);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
