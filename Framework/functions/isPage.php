<?php

if (!function_exists('isPage')) {

    function isPage($name): bool
    {
        return (str_replace('/', '', $_SERVER['REQUEST_URI']) === $name);
    }
}