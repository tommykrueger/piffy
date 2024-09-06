<?php

namespace Piffy\Framework;

class Log
{
    private const WARNING = E_USER_WARNING;

    public static function warning(string $message = ''): void
    {
        self::info($message);
    }

    public static function info(string $message = '', string $type = self::WARNING): void
    {
        trigger_error($message, $type);
    }
}