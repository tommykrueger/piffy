<?php

if (!function_exists('has')) {

    function has($obj, $key): bool
    {
        return (isset($obj->{$key}) && !empty($obj->{$key}));
    }
}