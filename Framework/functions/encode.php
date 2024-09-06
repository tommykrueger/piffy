<?php

if (!function_exists('encode')) {

    /**
     * Encode a string with quotes and double quotes. Used for json+ld fields and alt tags for <img>
     * @param $string
     * @return string
     */
    function encode($string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8', false);
    }
}