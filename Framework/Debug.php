<?php

namespace Piffy\Framework;

class Debug
{

    private static int $start = 0;

    private function __construct()
    {
    }

    public static function startTime(): void
    {
        self::$start = microtime(true);
    }

    public static function endTime(): void
    {
        $timeElapsedSeconds = microtime(true) - self::$start;
        echo '<!-- rendered in ' . round($timeElapsedSeconds, 4) . ' s -->';
    }

    /**
     * Debug output a variable or object
     *
     * @param mixed $data
     * @return void
     */
    public static function log(mixed $data): void
    {
        if (!DEBUG_LOG) {
            return;
        }

        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }
}