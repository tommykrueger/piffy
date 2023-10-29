<?php

namespace App\Models;

class BreadCrumb
{
    private static array $items = [];

    public static function addItem(array $item): void
    {
        self::$items[] = (object)$item;
    }

    public static function getItems(): array
    {
        return self::$items;
    }
}