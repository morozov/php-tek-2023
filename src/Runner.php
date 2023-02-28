<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023;

use Exception;

use function count;
use function printf;

use const PHP_EOL;

final readonly class Runner
{
    /** @param list<Test> $tests */
    public function run(array $tests): void
    {
        $connectionProvider = new ConnectionProvider();

        for ($i = 0, $count = count($tests); $i < $count; $i++) {
            $test = $tests[$i];

            printf('[%s]' . PHP_EOL, $test->toString());

            try {
                $test->run($connectionProvider);
            } catch (Exception $e) {
                echo $e->getMessage() . PHP_EOL;
            }

            if ($i >= $count - 1) {
                continue;
            }

            echo PHP_EOL;
        }
    }
}
