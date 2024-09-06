<?php

if (!function_exists('getUrl')) {

    /**
     * @param $data
     * @return string|string[]
     */
    function getUrl($data): array|string
    {
        $url = DOMAIN . $_SERVER['REQUEST_URI'];
        if (isset($data->slug)) {
            if (is_array($data->slug)) {
                $url = DOMAIN . $data->slug[0];
            } else {
                if ($data->slug[0] !== '/') {
                    $url = DOMAIN . '/' . $data->slug;
                } elseif ($data->slug !== '/') {
                    $url = DOMAIN . $data->slug;
                }
            }
        }

        /*
        else {
            $url = DOMAIN . '/' .$data->slug . '/';
        }
        */
        return str_replace(array('www.', 'http://'), array('', 'https://'), $url);
    }
}