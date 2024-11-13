<?php

namespace Piffy\Services;

class EventService
{
    private static array $events = [];

    public static function registerEventListener($eventName, $eventListener): void
    {
        self::$events[$eventName][] = $eventListener;
    }

    public static function trigger($eventName, $payload): void
    {
        foreach (self::$events[$eventName] as $event => $callback) {
            $test = new $callback();
            $test($payload);
        }
    }
}

