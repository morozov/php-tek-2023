<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L02\Driver;

use Morozov\PhpTek2023\Leaks\L02\Driver;
use Morozov\PhpTek2023\Leaks\L02\Message;
use OCILob;
use RuntimeException;

use function assert;
use function feof;
use function fread;
use function fseek;
use function fwrite;
use function oci_bind_by_name;
use function oci_commit;
use function oci_error;
use function oci_execute;
use function oci_fetch_row;
use function oci_free_statement;
use function oci_new_descriptor;
use function oci_parse;
use function tmpfile;

use const OCI_B_BLOB;
use const OCI_D_LOB;
use const OCI_NO_AUTO_COMMIT;

final readonly class Oci8Driver implements Driver
{
    public function __construct(private mixed $connection)
    {
    }

    public function persistMessage(Message $message): int
    {
        // see https://github.com/php/php-src/issues/8756
        $lob = @oci_new_descriptor($this->connection, OCI_D_LOB);

        $statement = oci_parse(
            $this->connection,
            <<<'SQL'
INSERT INTO messages (ID, ATTACHMENT)
VALUES (MESSAGES_SEQ.NEXTVAL, EMPTY_BLOB())
RETURNING ATTACHMENT INTO :attachment
SQL,
        );

        oci_bind_by_name($statement, ':attachment', $lob, -1, OCI_B_BLOB);

        if (! oci_execute($statement, OCI_NO_AUTO_COMMIT)) {
            throw new RuntimeException(oci_error($statement)['message']);
        }

        $copied = 0;
        while (! feof($message->attachment)) {
            $copied += $lob->write(fread($message->attachment, 8192));
        }

        oci_commit($this->connection);
        $lob->free();
        oci_free_statement($statement);

        return $copied;
    }

    public function fetchAttachment(): mixed
    {
        $statement = oci_parse($this->connection, 'SELECT ATTACHMENT FROM MESSAGES WHERE id = 1');

        oci_execute($statement);

        // see https://github.com/php/php-src/issues/8756
        $row = @oci_fetch_row($statement);

        $lob = $row[0];
        assert($lob instanceof OCILob);

        $tmp = tmpfile();

        while (! $lob->eof()) {
            fwrite($tmp, $lob->read(8192));
        }

        fseek($tmp, 0);

        return $tmp;
    }
}
