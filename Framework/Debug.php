<?php

namespace Piffy\Framework;

class Debug
{

    private static int $start = 0;

    private function __construct()
    {
    }

    public static function startTime()
    {
        self::$start = microtime(true);
    }

    public static function endTime()
    {
        $timeElapsedSeconds = microtime(true) - self::$start;
        echo '<!-- rendered in ' . round($timeElapsedSeconds, 4) . ' s -->';
    }
}