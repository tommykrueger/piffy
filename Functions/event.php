<?php

use Piffy\Services\EventService;

if (!function_exists('event')) {

    function event($eventName, $payload): void
    {
        EventService::trigger($eventName, $payload);
    }
}