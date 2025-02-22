<?php

if (!function_exists('getExcerpt'))
{
    function getExcerpt($string, $words = 20, $end = ' ...'): string
    {
        if (str_word_count($string) <= $words) {
            return $string;
        }

        // $string = strip_tags($string);
        $string = strip_tags($string);
        return preg_replace('/((\w+[\W|\s]*){'.($words-1).'}\w+|\W|\s)(?:(.*|\s))/', '${1}', $string) . '' . $end;
    }
}