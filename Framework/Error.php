<?php

namespace Piffy\Framework;

class Error
{
    private const WARNING = E_USER_WARNING;

    public static function warning(string $message = ''): void
    {
        self::log($message);
    }

    public static function log(string $message = '', string $type = self::WARNING): void
    {
        trigger_error($message, $type);
    }
}
