<?php

if (!function_exists('getKeywords')) {

    function getKeywords($data): string
    {

        $string = '';
        if (has($data, 'seo_keywords')) {
            $string = $data->seo_keywords;
        }
        return encode($string);
    }
}