<?php

if (!function_exists('getImage')) {

    function getImage($data)
    {
        if (isset($data->image) && !empty($data->image)) {
            return $data->image;
        }
        return false;
        // return DOMAIN . '/app/public/img/logo.png';
    }
}