<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L02\Driver;

use Morozov\PhpTek2023\Leaks\L02\Driver;
use Morozov\PhpTek2023\Leaks\L02\Message;
use RuntimeException;

use function fseek;
use function sqlsrv_errors;
use function sqlsrv_execute;
use function sqlsrv_fetch;
use function sqlsrv_get_field;
use function sqlsrv_phptype_stream;
use function sqlsrv_prepare;
use function sqlsrv_query;
use function sqlsrv_sqltype_varbinary;
use function stream_copy_to_stream;
use function tmpfile;

use const SQLSRV_ENC_BINARY;
use const SQLSRV_ERR_ERRORS;
use const SQLSRV_PARAM_IN;

final readonly class SqlSrvDriver implements Driver
{
    public function __construct(private mixed $connection)
    {
    }

    public function persistMessage(Message $message): null
    {
        $statement = sqlsrv_prepare(
            $this->connection,
            'INSERT INTO messages (attachment) VALUES (?)',
            [
                [
                    $message->attachment,
                    SQLSRV_PARAM_IN,
                    sqlsrv_phptype_stream(SQLSRV_ENC_BINARY),
                    sqlsrv_sqltype_varbinary('max'),
                ],
            ],
        );

        if ($statement === false) {
            $this->handleError();
        }

        if (! sqlsrv_execute($statement)) {
            $this->handleError();
        }

        return null;
    }

    public function fetchAttachment(): mixed
    {
        $statement = sqlsrv_query($this->connection, 'SELECT attachment FROM messages WHERE id = 1');

        if (sqlsrv_fetch($statement) === false) {
            $this->handleError();
        }

        $attachment = sqlsrv_get_field($statement, 0, sqlsrv_phptype_stream(SQLSRV_ENC_BINARY));

        // without this hack, once the stream leaves the scope of the function, it changes its type
        // from sqlsrv_stream to Unknown
        $tmp = tmpfile();
        stream_copy_to_stream($attachment, $tmp);
        fseek($tmp, 0);

        return $tmp;
    }

    /** @throws RuntimeException */
    private function handleError(): void
    {
        $error = sqlsrv_errors(SQLSRV_ERR_ERRORS);

        throw new RuntimeException($error[0]['message'], $error[0]['code']);
    }
}
