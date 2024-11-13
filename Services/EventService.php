<?php

namespace Piffy\Services;


class EventService
{
    public const POST_VOTED = 'POST_VOTED';

    private static array $events = [];

    public function __construct()
    {

    }

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

    /*
    public static function listen($name, $callback): void
    {
        self::$events[$name][] = $callback;
    }

    public static function dispatch($name, $argument = null): void
    {
        foreach (self::$events[$name] as $event => $callback) {
            if($argument && is_array($argument)) {
                call_user_func_array($callback, $argument);
            }
            elseif ($argument && !is_array($argument)) {
                call_user_func($callback, $argument);
            }
            else {
                call_user_func($callback);
            }
        }
    }
    */
}

