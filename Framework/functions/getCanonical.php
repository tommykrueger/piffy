<?php

if (!function_exists('getCanonical')) {

    /**
     * @param $data
     * @return string
     */
    function getCanonical($data): string
    {
        // var_dump($data);

        $url = $data->slug;

        if (substr($url, -1, 1) === '/') {
            $url = substr($url, 0, -1);
        }

        if (isArticle($data)) {
            $url .= '.html';
        } else {
            $url = $data->slug;
        }

        // $url = DOMAIN . $_SERVER['REQUEST_URI'];

        /*
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
        */

        return DOMAIN . $url;

        /*
        else {
            $url = DOMAIN . '/' .$data->slug . '/';
        }
        */
        //return str_replace(array('www.', 'http://'), array('', 'https://'), $url);
    }
}