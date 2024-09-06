<?php

namespace Piffy\Helpers;

final class TextHelper
{
    public static function getWordCount(string $content): int
    {
        return str_word_count(strip_tags($content));
    }
}