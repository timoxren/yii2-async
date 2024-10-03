<?php
/**
 * @link https://github.com/vuongxuongminh/yii2-async
 * @copyright Copyright (c) 2019 Vuong Xuong Minh
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace timoxren\async\runtime;

use Spatie\Async\Pool;
use Spatie\Async\Process\ParallelProcess;
use Spatie\Async\Process\Runnable;
use Spatie\Async\Process\SynchronousProcess;
use Spatie\Async\Runtime\ParentRuntime as BaseParentRuntime;
use Symfony\Component\Process\Process;

/**
 * ParentRuntime support invoke yii app in child runtime mode.
 *
 * @author Vuong Minh <vuongxuongminh@gmail.com>
 * @since 1.0.0
 */
class ParentRuntime extends BaseParentRuntime
{

    /**
     * @inheritDoc
     */
    public static function createProcess($task, ?int $outputLength = null, ?string $binary = 'php', ?int $max_input_size = 100000): Runnable
    {
        if (!self::$isInitialised) {
            self::init();
        }

        list($task, $appConfigFile) = $task;

        if (!Pool::isSupported()) {
            return SynchronousProcess::create($task, self::getId());
        }

        $process = new Process([
            'exec php',
            self::$childProcessScript,
            self::$autoloader,
            self::encodeTask($task),
            $appConfigFile
        ]);

        return ParallelProcess::create($process, self::getId());
    }
}
