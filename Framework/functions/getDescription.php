<?php

if (!function_exists('getDescription')) {

    function getDescription($data): string
    {
        if (has($data, 'seo_description')) {
            $string = $data->seo_description;
        } elseif (has($data, 'content')) {
            $string = $data->content;
        } elseif (has($data, 'name')) {
            $string = $data->name;
        } else {
            $string = $data->title ?? '';
        }
        return encode($string);
    }
}