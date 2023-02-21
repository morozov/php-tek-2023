<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023;

use InvalidArgumentException;

use function class_exists;
use function count;
use function sprintf;

final class Runner
{
    /** @param list<string> $arguments */
    public function run(array $arguments): void
    {
        $example = $arguments[1];
        $class   = 'Morozov\\PhpTek2023\\Examples\\' . $example;

        if (! class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Example "%s" not found', $example));
        }

        $runnable = new $class();

        $connectionProvider = new ConnectionProvider();

        if ($runnable instanceof Runnable) {
            $runnable->run($connectionProvider);
        } elseif ($runnable instanceof RunnableWithDriver) {
            if (count($arguments) < 3) {
                throw new InvalidArgumentException(sprintf('Driver is required for example "%s"', $example));
            }

            $runnable->run($connectionProvider, $arguments[2]);
        } else {
            throw new InvalidArgumentException(sprintf('Example "%s" is not runnable', $example));
        }
    }
}
