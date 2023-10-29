<?php

namespace App\Framework;
class File
{

    private static bool $isActive = false;

    private function __construct()
    {
    }

    public static function getFileChangedDateTime($path, $format = null)
    {
        $filetime = filemtime($path);
        $format = isset($format) ? $format : 'Y-m-d H:i:s';
        if ($filetime) {
            return date($format, $filetime);
        }

        // fallback
        return date($format, strtotime('21. Sep 2016'));
    }

}