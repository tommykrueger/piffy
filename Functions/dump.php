<?php

use Piffy\Framework\Debug;

if (!function_exists('dump')) {
    function dump(mixed ...$data): void
    {
        Debug::dump($data);
    }
}
