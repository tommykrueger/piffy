<?php

use Piffy\Framework\View;

if (!function_exists('post')) {

    function post($post, $data = null): void
    {
        View::post($post, $data);
    }
}
