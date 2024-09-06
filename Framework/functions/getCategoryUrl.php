<?php

use App\Models\Category;

if (!function_exists('getCategoryUrl')) {

    function getCategoryUrl($data, $fullPath = true): string
    {
        return Category::getUrl($data->id, $fullPath);
    }
}