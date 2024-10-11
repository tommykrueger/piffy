<?php

if (!function_exists('isHome')) {

    /**
     * Check weather we are on the homepage or not
     * @return bool
     */
    function isHome(): bool
    {
        return (isset($_SERVER['REQUEST_URI']) && in_array($_SERVER['REQUEST_URI'], ['', '/']));
    }
}