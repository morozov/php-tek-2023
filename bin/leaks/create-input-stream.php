<?php

declare(strict_types=1);

function create_input_stream()
{
    $fp = tmpfile();

    // Generate and write 20MB of data in 8KB chunks
    for ($i = 0; $i < 2560; $i++) {
        $chunk = random_bytes(8192);
        fwrite($fp, $chunk);
    }

    fseek($fp, 0);

    return $fp;
}
