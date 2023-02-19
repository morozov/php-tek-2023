<?php

function create_input_stream() {
    $fp = tmpfile();

    // Generate and write 20MB of data in 8KB chunks
    for ($i = 0; $i < 2560; $i++) {
        $chunk = random_bytes(8192);
        fwrite($fp, $chunk);
    }

    fseek($fp, 0);

    return $fp;
}

function copy_stream_to_dev_null($stream): int {
    $output = fopen('/dev/null', 'w');
    $copied = stream_copy_to_stream($stream, $output);
    fclose($output);

    return $copied;
}

function print_peak_memory_usage(): void {
    printf(
        'Peak memory usage: %s.' . PHP_EOL . PHP_EOL,
        format_as_mebibytes(memory_get_peak_usage())
    );
}

function format_as_mebibytes(int $value): string {
    return sprintf('%d MiB', $value / 1048576);
}
