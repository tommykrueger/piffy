<?php

namespace Piffy\Helpers;

class AlphabetList
{
    protected static array $list = [
        '0-9',
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z'
    ];

    public static function getList($exclude = []): array
    {
        return array_diff(self::$list, $exclude);
    }

}