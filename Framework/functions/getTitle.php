<?php

if (!function_exists('getTitle')) {
    
    function getTitle($data): string
    {
        
        if (has($data, 'seo_title')) {
            $string = $data->seo_title;
        } elseif (has($data, 'title')) {
            $string = $data->title ?? '';
        } else {
            $string = $data->name;
        }
        return encode($string);
    }
}