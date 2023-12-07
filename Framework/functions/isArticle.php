<?php

if (!function_exists('isArticle')) {

    /**
     * @param $data
     * @return bool
     */
    function isArticle($data): bool
    {
        return (isset($data->isArticle) && $data->isArticle);
    }
}