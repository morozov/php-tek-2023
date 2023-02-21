<?php

declare(strict_types=1);

require __DIR__ . '/../dbal.php';
require __DIR__ . '/blob-functions.php';

$conn = connect_to_sqlsrv()
    ->getNativeConnection();

$attachment = create_input_stream();

printf('Stream created.' . PHP_EOL);
print_peak_memory_usage();

$stmt = sqlsrv_prepare(
    $conn,
    'INSERT INTO messages (id, attachment) VALUES (NEXT VALUE FOR messages_id_seq, ?)',
    [
        [
            &$attachment,
            SQLSRV_PARAM_IN,
            sqlsrv_phptype_string(SQLSRV_ENC_CHAR),
        ],
    ],
);

sqlsrv_execute($stmt);

printf('Row inserted.' . PHP_EOL);
print_peak_memory_usage();

fclose($attachment);

$stmt = sqlsrv_query($conn, 'SELECT attachment FROM messages WHERE id = 1');

if (sqlsrv_fetch($stmt) === false) {
    die('Could not fetch row.');
}

$attachment = sqlsrv_get_field($stmt, 0, sqlsrv_phptype_stream(SQLSRV_ENC_BINARY));

$copied = copy_stream_to_dev_null($attachment);

printf('Copied %s.' . PHP_EOL, format_as_mebibytes($copied));

print_peak_memory_usage();

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
