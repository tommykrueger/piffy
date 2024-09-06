<?php

namespace Piffy\Framework;

class File
{
    private function __construct()
    {

    }

    /**
     * @param $path
     * @param $format
     * @return string
     */
    public static function getFileChangedDateTime($path, $format = null): string
    {
        if (is_file($path)) {
            $fileTime = filemtime($path);
            $format = $format ?? 'Y-m-d H:i:s';
            if ($fileTime) {
                return date($format, $fileTime);
            }
        }

        // fallback
        return date($format, strtotime('22. Sep 2023'));
    }

}