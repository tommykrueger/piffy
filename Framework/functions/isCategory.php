<?php

if (!function_exists('isCategory')) {

    /**
     * @param $data
     * @return bool
     */
    function isCategory($data): bool
    {
        return (isset($data->isCategory) && $data->isCategory);
    }
}