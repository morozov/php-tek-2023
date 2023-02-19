<?php

require __DIR__ . '/../dbal.php';
require __DIR__ . '/blob-functions.php';

// Connect to the database
$conn = connect_to_pgsql()
    ->getNativeConnection();

// Create the input stream
$attachment = create_input_stream();

printf('Stream created.' . PHP_EOL);
print_peak_memory_usage();

// Create an empty large object
$oid = pg_lo_create($conn);

// Open the large object for writing
$lo = pg_lo_open($conn, $oid, 'w');

// Write the stream data to the server using pg_lo_write
while ($data = fread($attachment, 8192)) {
    pg_lo_write($lo, pg_escape_bytea($conn, $data));
}

// Close the large object
//pg_lo_close($lo);

// Prepare the insert statement
$sql = "INSERT INTO messages (id, attachment) VALUES (nextval('messages_id_seq'), \$1)";
$stmt = pg_prepare($conn, 'insert_blob', $sql);

// Begin a transaction
pg_query($conn, 'BEGIN');

// Execute the prepared statement with a parameter for the large object OID
$result = pg_execute($conn, 'insert_blob', [$oid]);

// Check for errors
if (!$result) {
    echo "An error occurred: " . pg_last_error($conn) . "\n";
    pg_query($conn, "ROLLBACK");
    exit;
}

// Commit the transaction
pg_query($conn, "COMMIT");

printf('Row inserted.' . PHP_EOL);
print_peak_memory_usage();

// Clean up
fclose($attachment);
pg_free_result($result);
