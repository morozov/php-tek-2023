<?php

declare(strict_types=0);

namespace Morozov\PhpTek2023\Examples\E02BlobMemoryUsage\Driver;

use Morozov\PhpTek2023\Examples\E02BlobMemoryUsage\Driver;
use Morozov\PhpTek2023\Examples\E02BlobMemoryUsage\Message;
use PgSql\Connection;

use function fread;
use function pg_execute;
use function pg_free_result;
use function pg_last_error;
use function pg_lo_close;
use function pg_lo_create;
use function pg_lo_open;
use function pg_lo_write;
use function pg_prepare;
use function pg_query;
use function tmpfile;

use const PHP_EOL;

final readonly class PgSqlDriver implements Driver
{
    public function __construct(private Connection $connection)
    {
    }

    public function persistMessage(Message $message): int
    {
        // Begin a transaction
        pg_query($this->connection, 'BEGIN');

        // Create an empty large object
        $oid = pg_lo_create($this->connection);

        // Open the large object for writing
        $lo = pg_lo_open($this->connection, $oid, 'w');

        $copied = 0;

        // Write the stream data to the server using pg_lo_write
        while ($data = fread($message->attachment, 8192)) {
            $result = pg_lo_write($lo, $data);

            if ($result === false) {
                echo pg_last_error($this->connection), PHP_EOL;
                pg_query($this->connection, 'ROLLBACK');
                exit;
            }

            $copied += $result;
        }

        // Close the large object
        pg_lo_close($lo);

        // Prepare the insert statement
        $sql  = "INSERT INTO messages (id, attachment) VALUES (nextval('messages_id_seq'), \$1)";
        $stmt = pg_prepare($this->connection, 'insert_blob', $sql);

        // Execute the prepared statement with a parameter for the large object OID
        $result = pg_execute($this->connection, 'insert_blob', [$oid]);

        // Check for errors
        if (! $result) {
            echo 'An error occurred: ' . pg_last_error($this->connection) . "\n";
            pg_query($this->connection, 'ROLLBACK');
            exit;
        }

        // Commit the transaction
        pg_query($this->connection, 'COMMIT');

        // Clean up
        pg_free_result($result);

        return $copied;
    }

    public function fetchAttachment(): mixed
    {
        return tmpfile();
    }
}
