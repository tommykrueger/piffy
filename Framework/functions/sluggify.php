<?php

if (!function_exists('slugify')) {

    function slugify($string, $word_delimiter = '-'): string
    {
        // $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $input);

        $slug = str_ireplace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $string);

        $slug = iconv('UTF-8', 'utf-8//IGNORE', $slug);
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
        $slug = strtolower(trim($slug, '-'));
        $slug = preg_replace("/[\/_|+ -]+/", $word_delimiter, $slug);
        return $slug;
    }
}