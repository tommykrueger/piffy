<?php

namespace Piffy\Framework;

class File
{
    private static $isActive = false;

    private static $cachefile = null;

    private function __construct()
    {
    }

    public static function getFileChangedDateTime(string $path, string $format = null): string
    {
        $fileTime = filemtime($path);
        $format = $format ?? 'Y-m-d H:i:s';

        if ($fileTime) {
            return date($format, $fileTime);
        }

        // fallback
        return date($format, strtotime('22. Sep 2023'));
    }

}